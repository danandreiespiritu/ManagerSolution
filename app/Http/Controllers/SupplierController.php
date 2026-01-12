<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Repositories\Supplier\ISupplierRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SupplierController extends Controller
{
    public function __construct(protected ISupplierRepository $repo)
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

    public function index(Request $request): View
    {
        if (! $this->currentBusinessId()) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage suppliers.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $suppliers = $this->repo->paginate($request->user()->id, $perPage);

        return view('suppliers.index', compact('suppliers'));
    }

    public function show(Request $request, int $id): View
    {
        $supplier = $this->repo->getById($request->user()->id, $id);
        if (! $supplier) abort(404);
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Request $request, int $id): View
    {
        $supplier = $this->repo->getById($request->user()->id, $id);
        if (! $supplier) abort(404);
        return view('suppliers.edit', compact('supplier'));
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $this->repo->create($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Supplier created.');
    }

    public function update(SupplierRequest $request, int $id): RedirectResponse
    {
        $updated = $this->repo->update($request->user()->id, $id, $request->validated());

        if (! $updated) {
            return redirect()->back()->with('error', 'Unable to update supplier.');
        }

        return redirect()->back()->with('success', 'Supplier updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $deleted = $this->repo->delete($request->user()->id, $id);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Unable to delete supplier.');
        }

        return redirect()->back()->with('success', 'Supplier deleted.');
    }
}
