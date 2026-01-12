<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use App\Models\SupplierBill;
use App\Models\SupplierBillPayment;
use App\Models\SupplierCreditNote;
use App\Models\SupplierDebitNote;
use App\Models\SupplierPayment;
use App\Models\SupplierCreditNoteBill;
use App\Models\SupplierDebitNoteBill;
use App\Repositories\SupplierBill\ISupplierBillRepository;
use App\Repositories\SupplierCreditNote\ISupplierCreditNoteRepository;
use App\Repositories\SupplierDebitNote\ISupplierDebitNoteRepository;
use App\Repositories\SupplierPayment\ISupplierPaymentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierPayablesService
{
    public function __construct(
        protected LedgerPostingService $ledger,
        protected ISupplierBillRepository $billRepo,
        protected ISupplierPaymentRepository $paymentRepo,
        protected ISupplierCreditNoteRepository $creditRepo,
        protected ISupplierDebitNoteRepository $debitRepo,
    ) {
    }

    public function createBillAndPost(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $bill = $this->billRepo->createQuietly($userId, $data);

            $amount = (float) ($bill->total_amount ?? 0);
            if ($amount <= 0) {
                throw ValidationException::withMessages(['total_amount' => 'Bill amount must be greater than zero.']);
            }

            $entryDate = $bill->bill_date?->format('Y-m-d') ?? (string) $bill->bill_date;
            $periodId = $this->openPeriodIdForDate($bill->business_id, $entryDate);

            $expenseAccountId = $bill->expense_account_id ?? null;
            $apAccountId = $bill->ap_account_id ?? null;

            if (!$expenseAccountId || !$apAccountId) {
                throw ValidationException::withMessages([
                    'expense_account_id' => 'Expense/Asset account and Accounts Payable account are required for posting.',
                ]);
            }

            $this->ledger->post([
                'user_id' => $bill->user_id,
                'business_id' => $bill->business_id,
                'entry_date' => $entryDate,
                'reference_type' => 'SupplierBill',
                'reference_id' => $bill->id,
                'description' => "Supplier bill #{$bill->bill_number}",
                'accounting_period_id' => $periodId,
                'created_by' => $bill->user_id,
                'lines' => [
                    ['account_id' => $expenseAccountId, 'debit_amount' => $amount],
                    ['account_id' => $apAccountId, 'credit_amount' => $amount, 'supplier_id' => $bill->supplier_id],
                ],
            ]);

            // Status is driven by allocations; initially Unpaid
            $bill->status = 'Unpaid';
            $bill->save();

            return $bill->fresh();
        });
    }

    public function createDebitNoteAndPost(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $note = $this->debitRepo->createQuietly($userId, $data);

            $amount = (float) ($note->total_amount ?? 0);
            if ($amount <= 0) {
                throw ValidationException::withMessages(['total_amount' => 'Debit note amount must be greater than zero.']);
            }

            $entryDate = $note->debit_date?->format('Y-m-d') ?? (string) $note->debit_date;
            $periodId = $this->openPeriodIdForDate($note->business_id, $entryDate);

            $expenseAccountId = $note->expense_account_id ?? null;
            $apAccountId = $note->ap_account_id ?? null;

            if (!$expenseAccountId || !$apAccountId) {
                throw ValidationException::withMessages([
                    'expense_account_id' => 'Expense/Asset account and Accounts Payable account are required for posting.',
                ]);
            }

            // Supplier debit note increases AP (same posting as bill)
            $this->ledger->post([
                'user_id' => $note->user_id,
                'business_id' => $note->business_id,
                'entry_date' => $entryDate,
                'reference_type' => 'SupplierDebitNote',
                'reference_id' => $note->id,
                'description' => "Supplier debit note #{$note->debit_note_number}",
                'accounting_period_id' => $periodId,
                'created_by' => $note->user_id,
                'lines' => [
                    ['account_id' => $expenseAccountId, 'debit_amount' => $amount],
                    ['account_id' => $apAccountId, 'credit_amount' => $amount, 'supplier_id' => $note->supplier_id],
                ],
            ]);

            $note->status = $note->status ?: 'Open';
            $note->save();

            // Allocate to bills if provided
            $allocs = $data['allocations'] ?? [];
            if (is_array($allocs)) {
                foreach ($allocs as $a) {
                    $billId = (int) ($a['bill_id'] ?? 0);
                    $allocAmount = (float) ($a['amount'] ?? 0);
                    if ($billId && $allocAmount > 0) {
                        $this->allocateDebitNoteToBill($userId, $note->id, $billId, $allocAmount);
                    }
                }
            }

            return $note->fresh();
        });
    }

    public function createCreditNoteAndPost(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            /** @var SupplierCreditNote $note */
            $note = $this->creditRepo->create($userId, $data);

            $amount = (float) ($note->total_amount ?? 0);
            if ($amount <= 0) {
                throw ValidationException::withMessages(['total_amount' => 'Credit note amount must be greater than zero.']);
            }

            $entryDate = $note->credit_date?->format('Y-m-d') ?? (string) $note->credit_date;
            $periodId = $this->openPeriodIdForDate($note->business_id, $entryDate);

            $apAccountId = $note->ap_account_id ?? null;
            $offsetAccountId = $note->offset_account_id ?? null;

            if (!$apAccountId || !$offsetAccountId) {
                throw ValidationException::withMessages([
                    'offset_account_id' => 'Accounts Payable account and offset (expense/asset) account are required for posting.',
                ]);
            }

            // Supplier credit note reduces AP: Dr AP, Cr Offset
            $this->ledger->post([
                'user_id' => $note->user_id,
                'business_id' => $note->business_id,
                'entry_date' => $entryDate,
                'reference_type' => 'SupplierCreditNote',
                'reference_id' => $note->id,
                'description' => "Supplier credit note #{$note->credit_note_number}",
                'accounting_period_id' => $periodId,
                'created_by' => $note->user_id,
                'lines' => [
                    ['account_id' => $apAccountId, 'debit_amount' => $amount, 'supplier_id' => $note->supplier_id],
                    ['account_id' => $offsetAccountId, 'credit_amount' => $amount],
                ],
            ]);

            $note->status = $note->status ?: 'Open';
            $note->save();

            // Allocate to bills if provided
            $allocs = $data['allocations'] ?? [];
            if (is_array($allocs)) {
                foreach ($allocs as $a) {
                    $billId = (int) ($a['bill_id'] ?? 0);
                    $allocAmount = (float) ($a['amount'] ?? 0);
                    if ($billId && $allocAmount > 0) {
                        $this->allocateCreditNoteToBill($userId, $note->id, $billId, $allocAmount);
                    }
                }
            }

            return $note->fresh();
        });
    }

    public function createPaymentAndPost(int $userId, array $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            /** @var SupplierPayment $payment */
            $payment = $this->paymentRepo->create($userId, $data);

            $amount = (float) ($payment->amount ?? 0);
            if ($amount <= 0) {
                throw ValidationException::withMessages(['amount' => 'Payment amount must be greater than zero.']);
            }

            $entryDate = $payment->payment_date?->format('Y-m-d') ?? (string) $payment->payment_date;
            $periodId = $this->openPeriodIdForDate($payment->business_id, $entryDate);

            $cashAccountId = $payment->cash_account_id ?? null;
            $apAccountId = $payment->ap_account_id ?? null;

            if (!$cashAccountId || !$apAccountId) {
                throw ValidationException::withMessages([
                    'cash_account_id' => 'Cash/Bank account and Accounts Payable account are required for posting.',
                ]);
            }

            // Supplier payment reduces AP: Dr AP, Cr Cash/Bank
            $this->ledger->post([
                'user_id' => $payment->user_id,
                'business_id' => $payment->business_id,
                'entry_date' => $entryDate,
                'reference_type' => 'SupplierPayment',
                'reference_id' => $payment->id,
                'description' => "Supplier payment #{$payment->id}",
                'accounting_period_id' => $periodId,
                'created_by' => $payment->user_id,
                'lines' => [
                    ['account_id' => $apAccountId, 'debit_amount' => $amount, 'supplier_id' => $payment->supplier_id],
                    ['account_id' => $cashAccountId, 'credit_amount' => $amount],
                ],
            ]);

            $payment->status = $payment->status ?: 'Posted';
            $payment->save();

            // Allocate to bills if provided
            $allocs = $data['allocations'] ?? [];
            if (is_array($allocs)) {
                foreach ($allocs as $a) {
                    $billId = (int) ($a['bill_id'] ?? 0);
                    $allocAmount = (float) ($a['amount'] ?? 0);
                    if ($billId && $allocAmount > 0) {
                        $this->allocatePaymentToBill($userId, $payment->id, $billId, $allocAmount);
                    }
                }
            }

            return $payment->fresh();
        });
    }

    public function allocatePaymentToBill(int $userId, int $paymentId, int $billId, float $amount): bool
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages(['allocations' => 'Allocation amount must be greater than zero.']);
        }

        /** @var SupplierPayment|null $payment */
        $payment = SupplierPayment::where('user_id', $userId)->where('id', $paymentId)->first();
        /** @var SupplierBill|null $bill */
        $bill = SupplierBill::where('user_id', $userId)->where('id', $billId)->first();

        if (! $payment || ! $bill) {
            throw ValidationException::withMessages(['allocations' => 'Payment or Bill not found.']);
        }

        if ((int) $payment->supplier_id !== (int) $bill->supplier_id) {
            throw ValidationException::withMessages(['allocations' => 'Bill supplier must match payment supplier.']);
        }

        if ($payment->business_id && $bill->business_id && (int) $payment->business_id !== (int) $bill->business_id) {
            throw ValidationException::withMessages(['allocations' => 'Bill business must match payment business.']);
        }

        // Ensure allocations do not exceed payment or bill outstanding
        $newPaymentAllocated = $payment->allocatedAmount() + $amount;
        if ($newPaymentAllocated > ((float) $payment->amount) + 0.0001) {
            throw ValidationException::withMessages(['allocations' => 'Total allocation exceeds payment amount.']);
        }

        $newBillApplied = $bill->appliedAmount() + $amount;
        if ($newBillApplied > ((float) $bill->total_amount) + 0.0001) {
            throw ValidationException::withMessages(['allocations' => 'Allocation exceeds bill amount.']);
        }

        SupplierBillPayment::create([
            'user_id' => $userId,
            'business_id' => $payment->business_id ?? $bill->business_id ?? null,
            'supplier_payment_id' => $payment->id,
            'supplier_bill_id' => $bill->id,
            'amount' => $amount,
        ]);

        $this->refreshBillStatus($bill);

        return true;
    }

    public function allocateCreditNoteToBill(int $userId, int $noteId, int $billId, float $amount): bool
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages(['allocations' => 'Allocation amount must be greater than zero.']);
        }

        $note = SupplierCreditNote::where('user_id', $userId)->where('id', $noteId)->first();
        $bill = SupplierBill::where('user_id', $userId)->where('id', $billId)->first();

        if (! $note || ! $bill) {
            throw ValidationException::withMessages(['allocations' => 'Credit note or Bill not found.']);
        }

        if ((int) $note->supplier_id !== (int) $bill->supplier_id) {
            throw ValidationException::withMessages(['allocations' => 'Bill supplier must match credit note supplier.']);
        }

        if ($note->business_id && $bill->business_id && (int) $note->business_id !== (int) $bill->business_id) {
            throw ValidationException::withMessages(['allocations' => 'Bill business must match credit note business.']);
        }

        $newNoteAllocated = $note->allocatedAmount() + $amount;
        if ($newNoteAllocated > ((float) $note->total_amount) + 0.0001) {
            throw ValidationException::withMessages(['allocations' => 'Total allocation exceeds credit note amount.']);
        }

        $newBillApplied = $bill->appliedAmount() + $amount;
        if ($newBillApplied > ((float) $bill->total_amount) + 0.0001) {
            throw ValidationException::withMessages(['allocations' => 'Allocation exceeds bill amount.']);
        }

        SupplierCreditNoteBill::create([
            'user_id' => $userId,
            'business_id' => $note->business_id ?? $bill->business_id ?? null,
            'supplier_credit_note_id' => $note->id,
            'supplier_bill_id' => $bill->id,
            'amount' => $amount,
        ]);

        $this->refreshBillStatus($bill);

        return true;
    }

    public function allocateDebitNoteToBill(int $userId, int $noteId, int $billId, float $amount): bool
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages(['allocations' => 'Allocation amount must be greater than zero.']);
        }

        $note = SupplierDebitNote::where('user_id', $userId)->where('id', $noteId)->first();
        $bill = SupplierBill::where('user_id', $userId)->where('id', $billId)->first();

        if (! $note || ! $bill) {
            throw ValidationException::withMessages(['allocations' => 'Debit note or Bill not found.']);
        }

        if ((int) $note->supplier_id !== (int) $bill->supplier_id) {
            throw ValidationException::withMessages(['allocations' => 'Bill supplier must match debit note supplier.']);
        }

        if ($note->business_id && $bill->business_id && (int) $note->business_id !== (int) $bill->business_id) {
            throw ValidationException::withMessages(['allocations' => 'Bill business must match debit note business.']);
        }

        $newNoteAllocated = $note->allocatedAmount() + $amount;
        if ($newNoteAllocated > ((float) $note->total_amount) + 0.0001) {
            throw ValidationException::withMessages(['allocations' => 'Total allocation exceeds debit note amount.']);
        }

        // For debit notes we must ensure allocation doesn't create invalid states; allow increasing bill outstanding
        SupplierDebitNoteBill::create([
            'user_id' => $userId,
            'business_id' => $note->business_id ?? $bill->business_id ?? null,
            'supplier_debit_note_id' => $note->id,
            'supplier_bill_id' => $bill->id,
            'amount' => $amount,
        ]);

        $this->refreshBillStatus($bill);

        return true;
    }

    private function refreshBillStatus(SupplierBill $bill): void
    {
        $balance = $bill->balanceDue();

        if ($balance <= 0.0001) {
            $bill->status = 'Paid';
        } elseif ($bill->appliedAmount() > 0) {
            $bill->status = 'PartiallyPaid';
        } else {
            $bill->status = 'Unpaid';
        }

        $bill->save();
    }

    private function openPeriodIdForDate(?int $businessId, ?string $date): ?int
    {
        if (! $businessId || ! $date) return null;

        $p = AccountingPeriod::where('business_id', $businessId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('is_closed', false)
            ->first();

        return $p ? (int) $p->id : null;
    }
}
