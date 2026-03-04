<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use App\Models\ChartofAccounts;
use App\Models\AccountingPeriod;
use Illuminate\Validation\Rule;

class JournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id ?? null;
        } elseif (session('current_business_id')) {
            $businessId = (int) session('current_business_id');
        }

        return [
            'entry_date' => ['required','date'],
            'accounting_period_id' => ['nullable','integer','exists:accounting_periods,id'],
            'reference_type' => ['nullable','string','max:50'],
            'reference_id' => ['nullable','string','max:255'],
            'description' => ['required','string','max:2000'],
            'lines' => ['required','array','min:2'],
            'lines.*.account_id' => [
                'required',
                'integer',
                Rule::exists('chart_of_accounts', 'id')->where(fn ($q) => $q->where('business_id', $businessId ?? 0)),
            ],
            'lines.*.description' => ['nullable','string','max:2000'],
            'lines.*.cash_category' => ['nullable', Rule::in([
                'Operating activities',
                'Investing activities',
                'Financing activities',
                'Operational Activities',
                'Investing Activities',
                'Financing Activities',
            ])],
            'lines.*.debit_amount' => ['nullable','numeric','min:0'],
            'lines.*.credit_amount' => ['nullable','numeric','min:0'],
            'auto_adjust' => ['nullable','boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $lines = $this->input('lines', []);
            $totalDebit = '0.00';
            $totalCredit = '0.00';
            $hasDebitLine = false;
            $hasCreditLine = false;
            $accountIds = [];

            $toNum = static function ($value): string {
                if ($value === null) return '0.00';
                if (is_string($value) && trim($value) === '') return '0.00';
                if (!is_numeric($value)) return '0.00';
                // normalize scale to 2 decimals; input is already validated as numeric
                return number_format((float) $value, 2, '.', '');
            };

            // determine current business context when available
            $businessId = null;
            if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
                $businessId = $b->id;
            } elseif (session('current_business_id')) {
                $businessId = session('current_business_id');
            }

            foreach ($lines as $ln) {
                $debit = $toNum(Arr::get($ln, 'debit_amount', 0));
                $credit = $toNum(Arr::get($ln, 'credit_amount', 0));
                $accountId = Arr::get($ln, 'account_id');

                // Track account IDs for duplicate check
                if ($accountId) {
                    $accountIds[] = $accountId;
                }

                // Each line must have at least one positive amount (debit or credit or both)
                // Auto-adjust will handle any imbalances
                if (bccomp($debit, '0', 2) !== 1 && bccomp($credit, '0', 2) !== 1) {
                    $v->errors()->add('lines', 'Each line must have a debit or credit amount greater than zero.');
                }

                if (bccomp($debit, '0', 2) === 1) $hasDebitLine = true;
                if (bccomp($credit, '0', 2) === 1) $hasCreditLine = true;

                $totalDebit = bcadd($totalDebit, $debit, 2);
                $totalCredit = bcadd($totalCredit, $credit, 2);
            }

            // Validate no duplicate accounts
            $uniqueAccountIds = array_unique(array_filter($accountIds));
            if (count($accountIds) !== count($uniqueAccountIds)) {
                $v->errors()->add('lines', 'Duplicate accounts are not allowed. Each account can only appear once in a journal entry.');
            }

            // Check each account's metadata (active + same business)
            $accountIds = array_values(array_unique(array_filter(array_map(fn($ln) => Arr::get($ln, 'account_id'), $lines))));
            if (count($accountIds) > 0) {
                $accounts = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
                    ->whereIn('id', $accountIds)
                    ->get()
                    ->keyBy('id');

                // Helper to detect sub-ledger accounts
                $isReceivable = function ($acct) {
                    if (! $acct) return false;
                    $name = strtolower($acct->account_name ?? '');
                    $group = strtolower($acct->account_group ?? '');
                    $code = strtolower($acct->account_code ?? '');
                    return str_contains($name, 'receiv') || str_contains($group, 'receiv') || str_contains($code, 'receiv') || str_contains($name, 'accounts receivable');
                };
                $isPayable = function ($acct) {
                    if (! $acct) return false;
                    $name = strtolower($acct->account_name ?? '');
                    $group = strtolower($acct->account_group ?? '');
                    $code = strtolower($acct->account_code ?? '');
                    return str_contains($name, 'payabl') || str_contains($group, 'payabl') || str_contains($code, 'payabl') || str_contains($name, 'accounts payable');
                };

                // Enforce per-line control account rules
                foreach ($lines as $i => $ln) {
                    $acctId = Arr::get($ln, 'account_id');
                    if (! $acctId) continue;
                    $acct = $accounts->get($acctId);
                    if (! $acct) continue;

                    if (! $acct->is_active) {
                        $v->errors()->add('lines', "Account {$acct->account_name} (ID: {$acctId}) is not active.");
                    }
                    if ($businessId && $acct->business_id != $businessId) {
                        $v->errors()->add('lines', "Account {$acct->account_name} (ID: {$acctId}) does not belong to the selected business.");
                    }

                    // Control accounts: allow AR/AP only when linked to sub-ledger; block other control accounts.
                    if (! empty($acct->is_control_account)) {
                        if (! $isReceivable($acct) && ! $isPayable($acct)) {
                            $v->errors()->add('lines', "Account {$acct->account_name} (ID: {$acctId}) is a control account and cannot be posted to directly. Use the related sub-ledger (invoice, bill, payment)."
                            );
                        }
                    }
                }
            }

            // Check for imbalance and automatically enable auto-adjust if needed
            $imbalance = bcsub((string)$totalDebit, (string)$totalCredit, 2);
            
            if (bccomp($imbalance, '0', 2) !== 0) {
                // Automatically enable auto-adjust for imbalanced entries
                $this->merge(['auto_adjust' => true]);
            }

            if (! $hasDebitLine || ! $hasCreditLine) {
                $v->errors()->add('lines', 'Entries must contain at least one debit line and one credit line.');
            }

            // Accounting period must be open
            $periodId = $this->input('accounting_period_id');
            $entryDate = $this->input('entry_date');
            if ($periodId) {
                $period = AccountingPeriod::find($periodId);
                if (! $period) {
                    $v->errors()->add('accounting_period_id', 'Selected accounting period not found.');
                } else {
                    if ($period->is_closed) {
                        $v->errors()->add('accounting_period_id', 'Accounting period must be OPEN.');
                    }
                    if ($businessId && $period->business_id != $businessId) {
                        $v->errors()->add('accounting_period_id', 'Selected accounting period does not belong to the selected business.');
                    }
                    // Ensure provided entry date falls within the selected period (inclusive)
                    $entryDate = $this->input('entry_date');
                    if ($entryDate) {
                        try {
                            $ed = \Carbon\Carbon::parse($entryDate)->startOfDay();
                            $start = $period->start_date->startOfDay();
                            $end = $period->end_date->endOfDay();
                            if (! $ed->betweenIncluded($start, $end)) {
                                $v->errors()->add('entry_date', 'Entry date must be within the selected accounting period.');
                            }
                        } catch (\Exception $e) {
                            $v->errors()->add('entry_date', 'Invalid entry date.');
                        }
                    }
                }
            } else {
                // if no period provided, ensure there exists an open period covering the entry date when business context exists
                if ($businessId && $entryDate) {
                    $found = AccountingPeriod::where('business_id', $businessId)
                        ->where('start_date', '<=', $entryDate)
                        ->where('end_date', '>=', $entryDate)
                        ->where('is_closed', false)
                        ->exists();

                    if (! $found) {
                        $v->errors()->add('accounting_period_id', 'Accounting period must be OPEN for the entry date.');
                    }
                }
            }
        });
    }
}
