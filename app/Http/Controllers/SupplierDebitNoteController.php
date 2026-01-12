<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierDebitNoteRequest;

use App\Repositories\SupplierDebitNote\ISupplierDebitNoteRepository;
use App\Services\SupplierPayablesService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierDebitNoteController extends Controller
{
    public function __construct(
        protected ISupplierDebitNoteRepository $repo,
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
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier debit notes.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $debitNotes = $this->repo->paginate($request->user()->id, $perPage);

        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_debit_notes.index', compact('debitNotes', 'suppliers', 'accounts'));
    }

    public function store(\App\Http\Requests\SupplierDebitNoteRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to create a supplier debit note.');
        }

        $this->service->createDebitNoteAndPost($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Supplier debit note posted.');
    }

    public function show(Request $request, int $id): View
    {
        $note = $this->repo->getById($request->user()->id, $id);
        if (! $note) abort(404);

        return view('supplier_debit_notes.show', compact('note'));
    }

    public function edit(Request $request, int $id): View
    {
        $note = $this->repo->getById($request->user()->id, $id);
        if (! $note) abort(404);

        $businessId = $this->currentBusinessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier debit notes.');
        }
        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_debit_notes.edit', compact('note', 'suppliers', 'accounts'));
    }

    public function update(\App\Http\Requests\SupplierDebitNoteRequest $request, int $id): RedirectResponse
    {
        $ok = $this->repo->update($request->user()->id, $id, $request->validated());
        if (! $ok) return redirect()->back()->with('error', 'Debit note not found or update failed.');
        return redirect()->route('supplierdebitnotes.index')->with('success', 'Supplier debit note updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $ok = $this->repo->delete($request->user()->id, $id);
        if (! $ok) return redirect()->back()->with('error', 'Debit note not found or delete failed.');
        return redirect()->route('supplierdebitnotes.index')->with('success', 'Supplier debit note deleted.');
    }
}
