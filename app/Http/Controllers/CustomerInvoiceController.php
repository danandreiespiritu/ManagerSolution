<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerInvoiceRequest;
use App\Models\Customer;
use App\Repositories\CustomerInvoice\ICustomerInvoiceRepository;

class CustomerInvoiceController extends Controller
{
    protected ICustomerInvoiceRepository $repo;

    public function __construct(ICustomerInvoiceRepository $repo)
    {
        $this->repo = $repo;
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
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage invoices.');
        }

        $perPage = (int) $request->get('per_page', 15);
        $invoices = $this->repo->paginate($request->user()->id, $perPage);

        $customers = Customer::where('user_id', $request->user()->id)
            ->orderBy('customer_name')
            ->get();

        return view('customer_invoices.index', compact('invoices', 'customers'));
    }

    public function indexByCustomer(Request $request, $customerId): View
    {
        if (! $this->currentBusinessId()) {
            return redirect()->route('dashboard')->with('error', 'Please select a business first to manage invoices.');
        }

        $customer = Customer::where('user_id', $request->user()->id)
            ->where('id', (int) $customerId)
            ->first();

        $invoices = $this->repo->getByCustomer($request->user()->id, (int) $customerId);

        return view('customer_invoices.index', compact('invoices', 'customer'));
    }

    public function store(CustomerInvoiceRequest $request): RedirectResponse
    {
        if (! $this->currentBusinessId()) {
            return back()->with('error', 'Please select a business first to create an invoice.');
        }

        $this->repo->create($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Invoice created.');
    }

    public function show($id)
    {
        $inv = $this->repo->getById(auth()->id(), (int) $id);

        if (! $inv) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        return view('customer_invoices.show', ['invoice' => $inv]);
    }

    public function edit($id)
    {
        $inv = $this->repo->getById(auth()->id(), (int) $id);

        if (! $inv) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        return view('customer_invoices.edit', ['invoice' => $inv]);
    }

    public function update(CustomerInvoiceRequest $request, $id): RedirectResponse
    {
        $updated = $this->repo->update($request->user()->id, (int) $id, $request->validated());

        if (! $updated) {
            return redirect()->back()->with('error', 'Unable to update invoice.');
        }

        return redirect()->back()->with('success', 'Invoice updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $deleted = $this->repo->delete(auth()->id(), (int) $id);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Unable to delete invoice.');
        }

        return redirect()->back()->with('success', 'Invoice deleted.');
    }
}
