<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoicePaymentRequest;
use App\Models\CustomerInvoice;
use App\Models\InvoicePayment;
use App\Models\Payment;
use App\Repositories\Payment\IPaymentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class InvoicePaymentController extends Controller
{
    public function __construct(private readonly IPaymentRepository $payments)
    {
    }

    private function currentBusinessId(): ?int
    {
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            return $b->id ?? null;
        }

        $id = session('current_business_id');
        return $id ? (int) $id : null;
    }

    public function create(): RedirectResponse
    {
        return redirect()->to(route('invoicepayments.index') . '#new-allocation');
    }

    public function index(): View
    {
        if (! $this->currentBusinessId()) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to allocate payments.');
        }

        $userId = auth()->id();

        $allocations = InvoicePayment::where('user_id', $userId)
            ->with([
                'payment.customer',
                'invoice.customer',
            ])
            ->latest()
            ->paginate(15);

        $payments = Payment::where('user_id', $userId)
            ->with('customer')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $invoices = CustomerInvoice::where('user_id', $userId)
            ->with('customer')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('invoice_payments.index', compact('allocations', 'payments', 'invoices'));
    }

    public function store(InvoicePaymentRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to allocate payments.');
        }

        $userId = (int) $request->user()->id;
        $amount = (float) $request->validated('amount');
        $paymentId = (int) $request->validated('payment_id');
        $invoiceId = (int) $request->validated('customer_invoice_id');

        $payment = Payment::where('user_id', $userId)->where('id', $paymentId)->first();
        $invoice = CustomerInvoice::where('user_id', $userId)->where('id', $invoiceId)->first();

        if (! $payment || ! $invoice) {
            return back()->with('error', 'Payment or invoice not found.');
        }

        if ($payment->customer_id && $invoice->customer_id && (int) $payment->customer_id !== (int) $invoice->customer_id) {
            throw ValidationException::withMessages([
                'customer_invoice_id' => 'Invoice customer must match payment customer.',
            ]);
        }

        if ($payment->business_id && $invoice->business_id && (int) $payment->business_id !== (int) $invoice->business_id) {
            throw ValidationException::withMessages([
                'customer_invoice_id' => 'Invoice business must match payment business.',
            ]);
        }

        $existingAllocated = (float) $payment->invoicePayments()->sum('amount');
        if (($existingAllocated + $amount) > ((float) $payment->amount) + 0.0001) {
            throw ValidationException::withMessages([
                'amount' => 'Total allocation exceeds payment amount.',
            ]);
        }

        $invoiceApplied = (float) $invoice->appliedAmount();
        if (($invoiceApplied + $amount) > ((float) $invoice->total_amount) + 0.0001) {
            throw ValidationException::withMessages([
                'amount' => 'Allocation exceeds invoice amount.',
            ]);
        }

        $ok = $this->payments->allocate($userId, $paymentId, $invoiceId, $amount);

        return $ok
            ? back()->with('success', 'Allocation created.')
            : back()->with('error', 'Unable to create allocation.');
    }
}
