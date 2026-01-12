<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PaymentRequest;
use App\Repositories\Payment\IPaymentRepository;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\CustomerInvoice;

class PaymentController extends Controller
{
    protected IPaymentRepository $repo;

    public function __construct(IPaymentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $payments = Payment::where('user_id', auth()->id())
            ->with(['customer','invoices'])
            ->orderByDesc('payment_date')
            ->paginate($perPage);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        // load customers and invoices for selection
        $customers = Customer::orderBy('customer_name')->get();
        $invoices = CustomerInvoice::orderByDesc('invoice_date')->get();

        return view('payments.create', compact('customers', 'invoices'));
    }

    public function store(PaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $payment = $this->repo->create($request->user()->id, $data);

        // allocate if allocations provided
        $allocs = $data['allocations'] ?? [];
        foreach ($allocs as $a) {
            $invoiceId = (int) ($a['invoice_id'] ?? 0);
            $amount = (float) ($a['amount'] ?? 0);
            if ($invoiceId && $amount > 0) {
                $this->repo->allocate($request->user()->id, $payment->id, $invoiceId, $amount);
            }
        }

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function show(Request $request, int $id): View
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->with(['customer', 'supplier', 'invoicePayments.invoice'])
            ->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    public function edit(Request $request, int $id): View
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->with(['customer', 'supplier', 'invoicePayments.invoice'])
            ->findOrFail($id);

        $customers = Customer::orderBy('customer_name')->get();

        return view('payments.edit', compact('payment', 'customers'));
    }

    public function update(PaymentRequest $request, int $id): RedirectResponse
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->with(['invoicePayments'])
            ->findOrFail($id);

        $data = $request->validated();

        // Only allow updating the payment fields (allocations are managed via create/allocate flow).
        unset($data['allocations']);

        $newAmount = isset($data['amount']) ? (float) $data['amount'] : (float) $payment->amount;
        $allocated = (float) $payment->invoicePayments->sum('amount');
        if ($newAmount + 0.0001 < $allocated) {
            return redirect()->back()->with('error', 'Payment amount cannot be less than the allocated total.');
        }

        $payment->fill($data);
        $payment->save();

        return redirect()->route('payments.show', $payment->id)->with('success', 'Payment updated.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::where('user_id', $request->user()->id)
            ->with(['invoicePayments.invoice'])
            ->findOrFail($id);

        $affectedInvoices = $payment->invoicePayments
            ->pluck('invoice')
            ->filter()
            ->unique('id');

        // delete allocations first
        $payment->invoicePayments()->delete();
        $payment->delete();

        // recalc invoice status after allocation removal
        foreach ($affectedInvoices as $inv) {
            $balance = (float) $inv->balanceDue();
            $applied = (float) $inv->appliedAmount();

            if ($balance <= 0.0001) {
                $inv->status = 'Paid';
            } elseif ($applied > 0.0001) {
                $inv->status = 'PartiallyPaid';
            } else {
                $inv->status = 'Unpaid';
            }
            $inv->save();
        }

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
