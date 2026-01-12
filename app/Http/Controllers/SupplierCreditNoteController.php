<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierCreditNoteRequest;

use App\Repositories\SupplierCreditNote\ISupplierCreditNoteRepository;
use App\Services\SupplierPayablesService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierCreditNoteController extends Controller
{
    public function __construct(
        protected ISupplierCreditNoteRepository $repo,
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
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier credit notes.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $creditNotes = $this->repo->paginate($request->user()->id, $perPage);

        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_credit_notes.index', compact('creditNotes', 'suppliers', 'accounts'));
    }

    public function store(\App\Http\Requests\SupplierCreditNoteRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to create a supplier credit note.');
        }

        $this->service->createCreditNoteAndPost($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Supplier credit note posted.');
    }

    public function show(Request $request, int $id): View
    {
        $note = $this->repo->getById($request->user()->id, $id);
        if (! $note) abort(404);

        return view('supplier_credit_notes.show', compact('note'));
    }

    public function edit(Request $request, int $id): View
    {
        $note = $this->repo->getById($request->user()->id, $id);
        if (! $note) abort(404);

        $businessId = $this->currentBusinessId();
        if (! $businessId) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage supplier credit notes.');
        }
        $suppliers = \App\Models\Supplier::where('business_id', $businessId)->get();
        $accounts = $this->accountsRepo->getByBusiness($businessId);

        return view('supplier_credit_notes.edit', compact('note', 'suppliers', 'accounts'));
    }

    public function update(\App\Http\Requests\SupplierCreditNoteRequest $request, int $id): RedirectResponse
    {
        $ok = $this->repo->update($request->user()->id, $id, $request->validated());
        if (! $ok) return redirect()->back()->with('error', 'Credit note not found or update failed.');
        return redirect()->route('suppliercreditnotes.index')->with('success', 'Supplier credit note updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $ok = $this->repo->delete($request->user()->id, $id);
        if (! $ok) return redirect()->back()->with('error', 'Credit note not found or delete failed.');
        return redirect()->route('suppliercreditnotes.index')->with('success', 'Supplier credit note deleted.');
    }
}
