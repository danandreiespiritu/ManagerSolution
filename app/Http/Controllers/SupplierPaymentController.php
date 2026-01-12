<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierPaymentRequest;

use App\Repositories\SupplierPayment\ISupplierPaymentRepository;
use App\Services\SupplierPayablesService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function __construct(
        protected ISupplierPaymentRepository $repo,
        protected SupplierPayablesService $service,
        protected \App\Repositories\ChartofAccounts\IChartofAccountsRepository $accountsRepo,
    ) {
    }

    private function currentBusinessId(): ?int
    {
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            return $b->id ?? null;
        }

        $id = session('current_business_id');
        return $id ? (int) $id : null;
    }

    public function index(Request $request): View
    {
        $businessId = $this->currentBusinessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier payments.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $payments = $this->repo->paginate($request->user()->id, $perPage);

        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_payments.index', compact('payments', 'suppliers', 'accounts'));
    }

    public function store(\App\Http\Requests\SupplierPaymentRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to create a supplier payment.');
        }

        $this->service->createPaymentAndPost($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Supplier payment posted.');
    }

    public function show(Request $request, int $id): View
    {
        $payment = $this->repo->getById($request->user()->id, $id);
        if (! $payment) abort(404);

        return view('supplier_payments.show', compact('payment'));
    }

    public function edit(Request $request, int $id): View
    {
        $payment = $this->repo->getById($request->user()->id, $id);
        if (! $payment) abort(404);

        $businessId = $this->currentBusinessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier payments.');
        }
        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_payments.edit', compact('payment', 'suppliers', 'accounts'));
    }

    public function update(\App\Http\Requests\SupplierPaymentRequest $request, int $id): RedirectResponse
    {
        $ok = $this->repo->update($request->user()->id, $id, $request->validated());
        if (! $ok) return redirect()->back()->with('error', 'Payment not found or update failed.');

        return redirect()->route('supplierpayments.index')->with('success', 'Supplier payment updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $ok = $this->repo->delete($request->user()->id, $id);
        if (! $ok) return redirect()->back()->with('error', 'Payment not found or delete failed.');

        return redirect()->route('supplierpayments.index')->with('success', 'Supplier payment deleted.');
    }
}
