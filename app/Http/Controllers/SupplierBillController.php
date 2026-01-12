<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierBillRequest;

use App\Repositories\SupplierBill\ISupplierBillRepository;
use App\Services\SupplierPayablesService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierBillController extends Controller
{
    public function __construct(
        protected ISupplierBillRepository $repo,
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
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier bills.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $bills = $this->repo->paginate($request->user()->id, $perPage);

        // auxiliary collections for the form
        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_bills.index', compact('bills', 'suppliers', 'accounts'));
    }

    public function store(SupplierBillRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to create a supplier bill.');
        }

        try {
            $this->service->createBillAndPost($request->user()->id, $request->validated());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Unable to post supplier bill. Please check your accounts and accounting period.')->withInput();
        }

        return redirect()->back()->with('success', 'Supplier bill posted.');
    }

    public function show(Request $request, int $id): View
    {
        $bill = $this->repo->getById($request->user()->id, $id);
        if (! $bill) abort(404);

        return view('supplier_bills.show', compact('bill'));
    }

    public function edit(Request $request, int $id): View
    {
        $bill = $this->repo->getById($request->user()->id, $id);
        if (! $bill) abort(404);

        $businessId = $this->currentBusinessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier bills.');
        }
        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_bills.edit', compact('bill', 'suppliers', 'accounts'));
    }

    public function update(SupplierBillRequest $request, int $id): RedirectResponse
    {
        $ok = $this->repo->update($request->user()->id, $id, $request->validated());
        if (! $ok) return redirect()->back()->with('error', 'Unable to update bill.');
        return redirect()->route('supplierbills.index')->with('success', 'Supplier bill updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $ok = $this->repo->delete($request->user()->id, $id);
        if (! $ok) return redirect()->back()->with('error', 'Unable to delete bill.');
        return redirect()->route('supplierbills.index')->with('success', 'Supplier bill deleted.');
    }
}
