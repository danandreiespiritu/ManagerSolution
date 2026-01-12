<?php

namespace App\Services;

use App\Models\ChartofAccounts;
use App\Repositories\JournalEntry\IJournalEntryRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\JournalEntryAudit;
use Carbon\Carbon;
use App\Services\PostingRules\PostingRuleRegistry;
use App\Models\PostingRuleSetting;

class LedgerPostingService
{
    public function __construct(
        protected IJournalEntryRepository $journalRepo,
        protected PostingRuleRegistry $ruleRegistry,
    ) {
    }

    /**
     * Post a double-entry journal entry with enforced rules.
     *
     * Expected payload keys:
     * - user_id, business_id, entry_date (Y-m-d), description
     * - reference_type, reference_id, accounting_period_id, created_by
     * - lines: array of ['account_id','debit_amount'|null,'credit_amount'|null,'customer_id'|null,'supplier_id'|null]
     */
    public function post(array $payload)
    {
        $this->validatePostingPayload($payload);

        return DB::transaction(function () use ($payload) {
            $entry = $this->journalRepo->create($payload);

            // audit
            try {
                JournalEntryAudit::create([
                    'journal_entry_id' => $entry->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'business_id' => $payload['business_id'] ?? null,
                    'action' => 'posted',
                    'details' => [
                        'reference_type' => $payload['reference_type'] ?? null,
                        'reference_id' => $payload['reference_id'] ?? null,
                        'entry_date' => $payload['entry_date'] ?? null,
                        'lines' => $payload['lines'] ?? [],
                    ],
                ]);
            } catch (\Exception $e) {
                // don't fail posting for audit write errors; just log
                report($e);
            }

            return $entry;
        });
    }

    /**
     * Post with a named business rule (e.g., 'AP', 'AR', 'GENERAL').
     * Rules may enforce additional expectations per domain.
     */
    public function postWithRule(string $rule, array $payload)
    {
        // Check per-business posting rule settings (if provided)
        $businessId = $payload['business_id'] ?? null;
        $setting = null;
        if ($businessId) {
            $setting = PostingRuleSetting::where('business_id', $businessId)
                ->where('rule_name', $rule)
                ->first();
        }

        if ($setting && ! $setting->enabled) {
            throw ValidationException::withMessages(['posting_rule' => "Posting rule '{$rule}' is disabled for this business."]);
        }

        $handler = $this->ruleRegistry->get($rule);
        if (! $handler) {
            throw ValidationException::withMessages(['posting_rule' => "Unknown posting rule: {$rule}"]);
        }

        // allow the rule to validate/transform the payload
        // inject persisted rule config if present so handlers can use it
        if ($setting && is_array($setting->config)) {
            $payload['posting_rule_config'] = $setting->config;
        }

        $payload = $handler->validate($payload);
        $payload['posting_rule'] = $rule;

        return $this->post($payload);
    }

    /**
     * Create a reversal journal entry for a given journal entry id.
     * Creates a new journal entry with reversed debit/credit amounts and links audit.
     */
    public function reverse(int $journalEntryId, int $performedByUserId, ?string $reason = null)
    {
        $orig = $this->journalRepo->getById($journalEntryId);
        if (! $orig) {
            throw ValidationException::withMessages(['journal_entry_id' => 'Original journal entry not found.']);
        }

        // Build reversed payload
        $lines = [];
        foreach ($orig->lines as $ln) {
            $lines[] = [
                'account_id' => $ln->account_id,
                'debit_amount' => (float) $ln->credit_amount,
                'credit_amount' => (float) $ln->debit_amount,
                'customer_id' => $ln->customer_id,
                'supplier_id' => $ln->supplier_id,
            ];
        }

        $payload = [
            'user_id' => $performedByUserId,
            'business_id' => $orig->business_id ?? null,
            'entry_date' => Carbon::now()->format('Y-m-d'),
            'reference_type' => 'Reversal',
            'reference_id' => $orig->id,
            'description' => 'Reversal of JE #' . $orig->id . ($reason ? ' - ' . $reason : ''),
            'accounting_period_id' => $orig->accounting_period_id ?? null,
            'created_by' => $performedByUserId,
            'lines' => $lines,
        ];

        // validate and post
        $this->validatePostingPayload($payload);

        return DB::transaction(function () use ($payload, $orig, $performedByUserId, $reason) {
            $reversal = $this->journalRepo->create($payload);

            // audit both original (mark reversed) and reversal
            try {
                JournalEntryAudit::create([
                    'journal_entry_id' => $orig->id,
                    'user_id' => $performedByUserId,
                    'business_id' => $orig->business_id ?? null,
                    'action' => 'marked_reversed',
                    'details' => ['reversal_id' => $reversal->id, 'reason' => $reason],
                ]);

                JournalEntryAudit::create([
                    'journal_entry_id' => $reversal->id,
                    'user_id' => $performedByUserId,
                    'business_id' => $reversal->business_id ?? null,
                    'action' => 'reversal_posted',
                    'details' => ['original_id' => $orig->id, 'reason' => $reason],
                ]);
            } catch (\Exception $e) {
                report($e);
            }

            return $reversal;
        });
    }

    private function validatePostingPayload(array $payload): void
    {
        $lines = $payload['lines'] ?? [];

        if (!is_array($lines) || count($lines) < 2) {
            throw ValidationException::withMessages(['lines' => 'At least two lines are required.']);
        }

        $totalDebit = '0.00';
        $totalCredit = '0.00';
        $hasDebit = false;
        $hasCredit = false;

        $toNum = static function ($value): string {
            if ($value === null) return '0.00';
            if (is_string($value) && trim($value) === '') return '0.00';
            if (!is_numeric($value)) return '0.00';
            return number_format((float) $value, 2, '.', '');
        };

        $businessId = $payload['business_id'] ?? null;

        $accountIds = [];
        foreach ($lines as $i => $ln) {
            $accountId = Arr::get($ln, 'account_id');
            if (!$accountId) {
                throw ValidationException::withMessages(["lines.$i.account_id" => 'Account is required.']);
            }
            $accountIds[] = (int) $accountId;

            $debit = $toNum(Arr::get($ln, 'debit_amount', 0));
            $credit = $toNum(Arr::get($ln, 'credit_amount', 0));

            if (bccomp($debit, '0', 2) === 1 && bccomp($credit, '0', 2) === 1) {
                throw ValidationException::withMessages(['lines' => 'A line cannot have both debit and credit amounts.']);
            }
            if (bccomp($debit, '0', 2) !== 1 && bccomp($credit, '0', 2) !== 1) {
                throw ValidationException::withMessages(['lines' => 'Each line must have a debit or credit amount greater than zero.']);
            }

            if (bccomp($debit, '0', 2) === 1) $hasDebit = true;
            if (bccomp($credit, '0', 2) === 1) $hasCredit = true;

            $totalDebit = bcadd($totalDebit, $debit, 2);
            $totalCredit = bcadd($totalCredit, $credit, 2);
        }

        if (!$hasDebit || !$hasCredit) {
            throw ValidationException::withMessages(['lines' => 'Entries must contain at least one debit line and one credit line.']);
        }

        if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
            throw ValidationException::withMessages(['lines' => 'Total debits must equal total credits.']);
        }

        // Validate accounts belong to business and require sub-ledger links for control accounts (AR/AP)
        $accounts = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
            ->whereIn('id', array_values(array_unique($accountIds)))
            ->get()
            ->keyBy('id');

        $isPayable = function ($acct) {
            if (!$acct) return false;
            $name = strtolower($acct->account_name ?? '');
            $group = strtolower($acct->account_group ?? '');
            $code = strtolower($acct->account_code ?? '');
            return str_contains($name, 'payabl') || str_contains($group, 'payabl') || str_contains($code, 'payabl') || str_contains($name, 'accounts payable');
        };
        $isReceivable = function ($acct) {
            if (!$acct) return false;
            $name = strtolower($acct->account_name ?? '');
            $group = strtolower($acct->account_group ?? '');
            $code = strtolower($acct->account_code ?? '');
            return str_contains($name, 'receiv') || str_contains($group, 'receiv') || str_contains($code, 'receiv') || str_contains($name, 'accounts receivable');
        };

        foreach ($lines as $i => $ln) {
            $acctId = (int) Arr::get($ln, 'account_id');
            $acct = $accounts->get($acctId);
            if (!$acct) {
                throw ValidationException::withMessages(["lines.$i.account_id" => "Account not found: {$acctId}"]); 
            }
            if (!$acct->is_active) {
                throw ValidationException::withMessages(['lines' => "Account {$acct->account_name} (ID: {$acctId}) is not active."]); 
            }
            if ($businessId && (int) $acct->business_id !== (int) $businessId) {
                throw ValidationException::withMessages(['lines' => "Account {$acct->account_name} (ID: {$acctId}) does not belong to the selected business."]); 
            }

            if ($isPayable($acct) && !Arr::get($ln, 'supplier_id')) {
                throw ValidationException::withMessages(["lines.$i.supplier_id" => "Supplier is required for payable account '{$acct->account_name}'."]);
            }
            if ($isReceivable($acct) && !Arr::get($ln, 'customer_id')) {
                throw ValidationException::withMessages(["lines.$i.customer_id" => "Customer is required for receivable account '{$acct->account_name}'."]);
            }
        }
    }
}
