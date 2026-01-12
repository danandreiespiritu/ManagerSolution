<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierBillPaymentRequest;
use App\Models\SupplierBill;
use App\Models\SupplierBillPayment;
use App\Models\SupplierPayment;
use App\Services\SupplierPayablesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierBillPaymentController extends Controller
{
    public function __construct(private readonly SupplierPayablesService $payables)
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
        return redirect()->to(route('supplierbillpayments.index') . '#new-allocation');
    }

    public function index(): View
    {
        if (! $this->currentBusinessId()) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to allocate payments.');
        }

        $userId = auth()->id();

        $allocations = SupplierBillPayment::where('user_id', $userId)
            ->with([
                'payment.supplier',
                'bill.supplier',
            ])
            ->latest()
            ->paginate(15);

        $payments = SupplierPayment::where('user_id', $userId)
            ->with('supplier')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $bills = SupplierBill::where('user_id', $userId)
            ->with('supplier')
            ->orderByDesc('bill_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return view('supplier_bill_payments.index', compact('allocations', 'payments', 'bills'));
    }

    public function store(SupplierBillPaymentRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to allocate payments.');
        }

        $userId = (int) $request->user()->id;
        $paymentId = (int) $request->validated('supplier_payment_id');
        $billId = (int) $request->validated('supplier_bill_id');
        $amount = (float) $request->validated('amount');

        $this->payables->allocatePaymentToBill($userId, $paymentId, $billId, $amount);

        return back()->with('success', 'Allocation created.');
    }
}
