@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Supplier Payments</h1>
            <p class="text-sm text-gray-600">Record payments made to suppliers.</p>
        </div>
       
    </div>

    <div class="mb-4 flex gap-3">
        <form method="GET" class="flex-1">
            <input name="q"
                   value="{{ request('q') }}"
                   placeholder="Search payments..."
                   class="w-full p-2 bg-white border border-gray-300 rounded shadow-sm text-gray-900" />
        </form>

        <a href="{{ route('supplierpayments.index') }}"
           class="px-3 py-2 bg-blue-100 text-blue-600 border border-blue-300 rounded text-sm hover:bg-blue-200">
            Refresh
        </a>
    </div>

    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <div class="overflow-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-700 text-xs uppercase border-b">
                    <tr>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Reference</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Amount</th>
                        <th class="py-2">Allocated</th>
                        <th class="py-2">Unallocated</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments ?? [] as $p)
                        <tr class="border-b hover:bg-gray-50 text-gray-900">
                            <td class="py-3">{{ $p->supplier->supplier_name ?? '—' }}</td>
                            <td class="py-3">{{ $p->reference }}</td>
                            <td class="py-3">{{ $p->payment_date }}</td>
                            <td class="py-3">{{ number_format($p->amount, 2) }}</td>
                            <td class="py-3">{{ number_format($p->allocatedAmount(), 2) }}</td>
                            <td class="py-3">{{ number_format($p->unallocatedAmount(), 2) }}</td>

                            <td class="py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('supplierpayments.show', $p->id) }}"
                                       class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200">
                                        View
                                    </a>

                                    <a href="{{ route('supplierpayments.edit', $p->id) }}"
                                       class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200">
                                        Edit
                                    </a>

                                    <form action="{{ route('supplierpayments.destroy', $p->id) }}"
                                          method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')

                                        <button class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200"
                                                onclick="return confirm('Delete this payment?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-600">
                                No supplier payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $payments->appends(request()->query())->links() }}</div>

    {{-- Record Payment Form --}}
    <div id="new-payment" class="mt-6 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">

        <h3 class="text-lg font-semibold text-gray-900 mb-3">Record Payment</h3>

        <form method="POST" action="{{ route('supplierpayments.store') }}">
            @csrf

            <div class="grid grid-cols-4 gap-3">

                <div class="col-span-2">
                    <label class="text-xs text-gray-700">Supplier</label>
                    <select name="supplier_id"
                            class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                        <option value="">Select supplier</option>
                        @foreach($suppliers ?? [] as $s)
                            <option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-700">Date</label>
                    <input
                        name="payment_date"
                        id="payment_date"
                        type="date"
                        placeholder="Select date"
                        class="cursor-pointer mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900 shadow-sm"
                    />
                </div>


                <div>
                    <label class="text-xs text-gray-700">Amount</label>
                    <input name="amount"
                           class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900" />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Cash Account</label>
                    <select name="cash_account_id"
                            class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                        <option value="">Cash account</option>
                        @foreach($accounts ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-700">AP Account</label>
                    <select name="ap_account_id"
                            class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                        <option value="">AP account</option>
                        @foreach($accounts ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-700">Payment Method (optional)</label>
                    <select name="payment_method"
                            class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                        <option value="">Select method</option>
                        <option value="bank_transfer">Bank transfer</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="card">Card</option>
                        <option value="eft">EFT</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-700">Reference (optional)</label>
                    <input name="reference"
                           class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900"
                           placeholder="Reference" />
                </div>

            </div>

            <div class="mt-4">
                <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                    Record Payment
                </button>
            </div>
        </form>
    </div>

</div>

<script>
flatpickr("#payment_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    defaultDate: "{{ old('payment_date') }}"
});
</script>

@endsection
