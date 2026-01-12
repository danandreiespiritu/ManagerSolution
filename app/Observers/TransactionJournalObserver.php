<?php

namespace App\Observers;

use App\Services\AutomaticJournalService;
use Illuminate\Support\Facades\Log;

class TransactionJournalObserver
{
    protected AutomaticJournalService $service;

    public function __construct()
    {
        $this->service = app(AutomaticJournalService::class);
    }

    public function created($model)
    {
        try {
            $class = get_class($model);
            switch ($class) {
                case \App\Models\CustomerInvoice::class:
                    $this->service->createForCustomerInvoice($model);
                    break;
                case \App\Models\SupplierBill::class:
                    $this->service->createForSupplierBill($model);
                    break;
                case \App\Models\Payment::class:
                    $this->service->createForPayment($model);
                    break;
                case \App\Models\CustomerCreditNote::class:
                    $this->service->createForCustomerCreditNote($model);
                    break;
                case \App\Models\SupplierDebitNote::class:
                    // Supplier debit notes are posted via SupplierPayablesService in normal flows; keep auto-journal conservative.
                    // If triggered, at least avoid incorrectly treating it as a supplier bill.
                    // No-op for now.
                    break;
                default:
                    // no-op
            }
        } catch (\Exception $e) {
            Log::error('TransactionJournalObserver error', ['error' => $e->getMessage()]);
        }
    }
}
