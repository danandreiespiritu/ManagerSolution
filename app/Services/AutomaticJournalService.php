<?php

namespace App\Services;

use App\Models\ChartofAccounts;
use App\Models\AccountingPeriod;
use App\Repositories\JournalEntry\IJournalEntryRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AutomaticJournalService
{
    protected IJournalEntryRepository $journalRepo;

    public function __construct(
        IJournalEntryRepository $journalRepo,
        protected LedgerPostingService $ledger,
        protected StandardCoaService $standardCoa,
    )
    {
        $this->journalRepo = $journalRepo;
    }

    protected function findByCodeOrKeywords(?int $businessId, string $code, array $keywords): ?ChartofAccounts
    {
        $acct = $this->standardCoa->findByCode($businessId, $code);
        if ($acct) {
            return $acct;
        }
        return $this->findAccountForKeywords($businessId, $keywords);
    }

    protected function findAccountForKeywords($businessId, array $keywords)
    {
        $q = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId));

        foreach ($keywords as $kw) {
            $found = (clone $q)->whereRaw('LOWER(account_name) LIKE ?', ["%{$kw}%"])->first();
            if ($found) return $found;
        }

        return null;
    }

    protected function openPeriodIdForDate($businessId, $date)
    {
        if (! $businessId || ! $date) return null;
        $p = AccountingPeriod::where('business_id', $businessId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('is_closed', false)
            ->first();
        return $p ? $p->id : null;
    }

    public function createForCustomerInvoice($invoice)
    {
        // attempt to find AR and Revenue accounts
        $businessId = $invoice->business_id ?? null;
        $ar = $this->findByCodeOrKeywords($businessId, '1100', ['receiv', 'accounts receivable']);
        $rev = $this->findByCodeOrKeywords($businessId, '4000', ['sales', 'revenue']);

        if (! $ar || ! $rev) {
            Log::warning('AutomaticJournal: missing AR or Revenue account', ['invoice_id' => $invoice->id]);
            return;
        }

        $amount = (float) ($invoice->total_amount ?? 0);
        if ($amount <= 0) return;

        $entryDate = $invoice->invoice_date?->format('Y-m-d') ?? ($invoice->invoice_date ?? null);
        $payload = [
            'user_id' => $invoice->user_id ?? null,
            'business_id' => $businessId,
            'entry_date' => $entryDate,
            'reference_type' => 'CustomerInvoice',
            'reference_id' => $invoice->id,
            'description' => "Auto journal for invoice #{$invoice->invoice_number}",
            'accounting_period_id' => $this->openPeriodIdForDate($businessId, $entryDate),
            'created_by' => $invoice->user_id ?? null,
            'lines' => [
                ['account_id' => $ar->id, 'debit_amount' => $amount, 'customer_id' => $invoice->customer_id ?? null],
                ['account_id' => $rev->id, 'credit_amount' => $amount],
            ],
        ];

        try {
            $this->ledger->post($payload);
        } catch (ValidationException $e) {
            Log::warning('AutomaticJournal: validation failed when creating journal for invoice', ['invoice_id' => $invoice->id, 'errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('AutomaticJournal: failed to create journal for invoice', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);
        }
    }

    public function createForCustomerCreditNote($note)
    {
        $businessId = $note->business_id ?? null;
        $ar = $this->findByCodeOrKeywords($businessId, '1100', ['receiv', 'accounts receivable']);

        // Revenue-side credit notes MUST hit 4020 (Sales Returns and Allowances) per standard COA.
        $fallbackCode = (string) config('standard_coa.fallbacks.revenue_credit_note', '4020');
        $text = (string) ($note->description ?? $note->notes ?? $note->memo ?? '');
        $offset = $this->standardCoa->resolveAccountForText($businessId, $text, $fallbackCode, ['4020']);

        if (! $ar || ! $offset) {
            Log::warning('AutomaticJournal: missing AR or offset account for customer credit note', ['credit_note_id' => $note->id]);
            return;
        }

        $amount = (float) ($note->total_amount ?? 0);
        if ($amount <= 0) return;

        $entryDate = $note->credit_date?->format('Y-m-d') ?? ($note->credit_date ?? null);

        $payload = [
            'user_id' => $note->user_id ?? null,
            'business_id' => $businessId,
            'entry_date' => $entryDate,
            'reference_type' => 'CustomerCreditNote',
            'reference_id' => $note->id,
            'description' => "Auto journal for customer credit note #{$note->credit_note_number}",
            'accounting_period_id' => $this->openPeriodIdForDate($businessId, $entryDate),
            'created_by' => $note->user_id ?? null,
            'lines' => [
                // Credit note reduces AR: Cr AR; and debits returns/revenue offset.
                ['account_id' => $offset->id, 'debit_amount' => $amount],
                ['account_id' => $ar->id, 'credit_amount' => $amount, 'customer_id' => $note->customer_id ?? null],
            ],
        ];

        try {
            $this->ledger->post($payload);
        } catch (ValidationException $e) {
            Log::warning('AutomaticJournal: validation failed when creating journal for customer credit note', ['credit_note_id' => $note->id, 'errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('AutomaticJournal: failed to create journal for customer credit note', ['credit_note_id' => $note->id, 'error' => $e->getMessage()]);
        }
    }

    public function createForSupplierBill($bill)
    {
        $businessId = $bill->business_id ?? null;
        $ap = null;
        $purch = null;

        // Prefer explicit accounts stored on the bill if available.
        if (!empty($bill->ap_account_id)) {
            $ap = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
                ->when($businessId, fn($q) => $q->where('business_id', $businessId))
                ->where('id', $bill->ap_account_id)
                ->first();
        }
        if (!empty($bill->expense_account_id)) {
            $purch = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
                ->when($businessId, fn($q) => $q->where('business_id', $businessId))
                ->where('id', $bill->expense_account_id)
                ->first();
        }

        // Fallback to keyword-based lookup.
        $ap = $ap ?: $this->findByCodeOrKeywords($businessId, '2000', ['payabl', 'accounts payable']);

        // Try standard keyword mapping by code first (rent/utilities/etc), then fallback to legacy name search.
        if (! $purch) {
            $text = (string) ($bill->description ?? $bill->notes ?? $bill->memo ?? '');
            $mappedCode = $this->standardCoa->resolveCodeForText($text);
            if ($mappedCode) {
                $purch = $this->standardCoa->findByCode($businessId, $mappedCode);
            }
        }
        $purch = $purch ?: $this->findAccountForKeywords($businessId, ['purchase', 'expense', 'cost of goods', 'cogs']);

        if (! $ap || ! $purch) {
            Log::warning('AutomaticJournal: missing AP or Purchase account', ['bill_id' => $bill->id]);
            return;
        }

        $amount = (float) ($bill->total_amount ?? 0);
        if ($amount <= 0) return;

        $entryDate = $bill->bill_date?->format('Y-m-d') ?? ($bill->bill_date ?? null);
        $payload = [
            'user_id' => $bill->user_id ?? null,
            'business_id' => $businessId,
            'entry_date' => $entryDate,
            'reference_type' => 'SupplierBill',
            'reference_id' => $bill->id,
            'description' => "Auto journal for supplier bill #{$bill->bill_number}",
            'accounting_period_id' => $this->openPeriodIdForDate($businessId, $entryDate),
            'created_by' => $bill->user_id ?? null,
            'lines' => [
                ['account_id' => $purch->id, 'debit_amount' => $amount],
                ['account_id' => $ap->id, 'credit_amount' => $amount, 'supplier_id' => $bill->supplier_id ?? null],
            ],
        ];

        try {
            $this->ledger->post($payload);
        } catch (ValidationException $e) {
            Log::warning('AutomaticJournal: validation failed when creating journal for bill', ['bill_id' => $bill->id, 'errors' => $e->errors()]);
        } catch (\Exception $e) {
            Log::error('AutomaticJournal: failed to create journal for bill', ['bill_id' => $bill->id, 'error' => $e->getMessage()]);
        }
    }

    public function createForPayment($payment)
    {
        $businessId = $payment->business_id ?? null;
        $cashAccountId = $payment->cash_account_id ?? null;
        $amount = (float) ($payment->amount ?? 0);
        if ($amount <= 0) return;

        $entryDate = $payment->payment_date?->format('Y-m-d') ?? ($payment->payment_date ?? null);

        // Customer payment -> Debit cash, Credit AR
        if ($payment->customer_id) {
            $ar = $this->findByCodeOrKeywords($businessId, '1100', ['receiv', 'accounts receivable']);
            if (! $ar || ! $cashAccountId) {
                Log::warning('AutomaticJournal: missing AR or cash account for payment', ['payment_id' => $payment->id]);
                return;
            }

            $payload = [
                'user_id' => $payment->user_id ?? null,
                'business_id' => $businessId,
                'entry_date' => $entryDate,
                'reference_type' => 'Payment',
                'reference_id' => $payment->id,
                'description' => "Auto journal for payment #{$payment->id}",
                'accounting_period_id' => $this->openPeriodIdForDate($businessId, $entryDate),
                'created_by' => $payment->user_id ?? null,
                'lines' => [
                    ['account_id' => $cashAccountId, 'debit_amount' => $amount],
                    ['account_id' => $ar->id, 'credit_amount' => $amount, 'customer_id' => $payment->customer_id],
                ],
            ];

            try {
                $this->ledger->post($payload);
            } catch (ValidationException $e) {
                Log::warning('AutomaticJournal: validation failed when creating journal for payment', ['payment_id' => $payment->id, 'errors' => $e->errors()]);
            } catch (\Exception $e) {
                Log::error('AutomaticJournal: failed to create journal for payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
            return;
        }

        // Supplier payment -> Debit AP, Credit cash
        if ($payment->supplier_id) {
            $ap = $this->findByCodeOrKeywords($businessId, '2000', ['payabl', 'accounts payable']);
            if (! $ap || ! $cashAccountId) {
                Log::warning('AutomaticJournal: missing AP or cash account for supplier payment', ['payment_id' => $payment->id]);
                return;
            }

            $payload = [
                'user_id' => $payment->user_id ?? null,
                'business_id' => $businessId,
                'entry_date' => $entryDate,
                'reference_type' => 'Payment',
                'reference_id' => $payment->id,
                'description' => "Auto journal for payment #{$payment->id}",
                'accounting_period_id' => $this->openPeriodIdForDate($businessId, $entryDate),
                'created_by' => $payment->user_id ?? null,
                'lines' => [
                    ['account_id' => $ap->id, 'debit_amount' => $amount, 'supplier_id' => $payment->supplier_id],
                    ['account_id' => $cashAccountId, 'credit_amount' => $amount],
                ],
            ];

            try {
                $this->ledger->post($payload);
            } catch (ValidationException $e) {
                Log::warning('AutomaticJournal: validation failed when creating journal for payment', ['payment_id' => $payment->id, 'errors' => $e->errors()]);
            } catch (\Exception $e) {
                Log::error('AutomaticJournal: failed to create journal for payment', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
            return;
        }
    }
}
