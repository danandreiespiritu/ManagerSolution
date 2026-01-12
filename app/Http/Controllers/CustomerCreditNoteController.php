<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreditNoteRequest;
use App\Repositories\CustomerCreditNote\ICustomerCreditNoteRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerCreditNoteController extends Controller
{
    public function __construct(protected ICustomerCreditNoteRepository $repo)
    {
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 15);
        $creditNotes = $this->repo->paginate($request->user()->id, $perPage);

        $customers = \App\Models\Customer::orderBy('customer_name')->get();

        return view('customer_credit_notes.index', compact('creditNotes', 'customers'));
    }

    public function store(CustomerCreditNoteRequest $request): RedirectResponse
    {
        $this->repo->create($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Customer credit note created.');
    }

    public function show(Request $request, int $id): View
    {
        $note = $this->repo->getById($request->user()->id, $id);
        if (! $note) abort(404);

        return view('customer_credit_notes.show', compact('note'));
    }

    public function edit(Request $request, int $id): View
    {
        $note = $this->repo->getById($request->user()->id, $id);
        if (! $note) abort(404);

        $customers = \App\Models\Customer::orderBy('customer_name')->get();

        return view('customer_credit_notes.edit', compact('note', 'customers'));
    }

    public function update(CustomerCreditNoteRequest $request, int $id): RedirectResponse
    {
        $ok = $this->repo->update($request->user()->id, $id, $request->validated());
        if (! $ok) return redirect()->back()->with('error', 'Credit note not found or update failed.');

        return redirect()->route('customercreditnotes.index')->with('success', 'Customer credit note updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $ok = $this->repo->delete($request->user()->id, $id);
        if (! $ok) return redirect()->back()->with('error', 'Credit note not found or delete failed.');

        return redirect()->route('customercreditnotes.index')->with('success', 'Customer credit note deleted.');
    }
}
