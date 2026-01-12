<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Repositories\Customer\ICustomerRepository;

class CustomerController extends Controller
{
    protected ICustomerRepository $repo;

    public function __construct(ICustomerRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 15);
        $customers = $this->repo->paginate($request->user()->id, $perPage);

        return view('customers.index', compact('customers'));
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $this->repo->create($request->user()->id, $request->validated());

        return redirect()->back()->with('success', 'Customer created.');
    }

    public function edit($id)
    {
        $cust = $this->repo->getById(auth()->id(), (int) $id);

        if (! $cust) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        return view('customers.edit', ['customer' => $cust]);
    }

    public function update(CustomerRequest $request, $id): RedirectResponse
    {
        $updated = $this->repo->update($request->user()->id, (int) $id, $request->validated());

        if (! $updated) {
            return redirect()->back()->with('error', 'Unable to update customer.');
        }

        return redirect()->back()->with('success', 'Customer updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $deleted = $this->repo->delete(auth()->id(), (int) $id);

        if (! $deleted) {
            return redirect()->back()->with('error', 'Unable to delete customer.');
        }

        return redirect()->back()->with('success', 'Customer deleted.');
    }
}
