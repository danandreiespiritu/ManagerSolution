<?php
namespace App\Repositories\Payment;

use App\Models\Payment as PaymentModel;
use App\Models\InvoicePayment;
use App\Models\CustomerInvoice;
use Illuminate\Support\Str;

class PaymentRepository implements IPaymentRepository
{
    public function create(int $userId, array $data): PaymentModel
    {
        $payload = array_merge($data, ['user_id' => $userId]);

        return PaymentModel::create($payload);
    }

    public function allocate(int $userId, int $paymentId, int $invoiceId, float $amount): bool
    {
        $payment = PaymentModel::where('user_id', $userId)->where('id', $paymentId)->first();
        $invoice = CustomerInvoice::where('id', $invoiceId)->where('user_id', $userId)->first();

        if (! $payment || ! $invoice) return false;

        // create allocation record
        $alloc = InvoicePayment::create([
            'user_id' => $userId,
            'business_id' => $payment->business_id ?? $invoice->business_id ?? null,
            'payment_id' => $payment->id,
            'customer_invoice_id' => $invoice->id,
            'amount' => $amount,
        ]);

        // update invoice status
        $balance = $invoice->balanceDue();

        if ($balance <= 0.0001) {
            $invoice->status = 'Paid';
            $invoice->save();
        } elseif ($invoice->appliedAmount() > 0) {
            $invoice->status = 'PartiallyPaid';
            $invoice->save();
        }

        return (bool) $alloc;
    }
}
