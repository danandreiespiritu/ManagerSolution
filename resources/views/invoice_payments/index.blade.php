<x-app-layout>
    <div class="py-6 text-black">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Invoice Payment Allocations</h1>
                    <p class="text-sm text-gray-600 mt-1">Link payments to specific invoices</p>
                </div>
                <a href="#new-allocation" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">New Allocation</a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded shadow border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Date</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Customer</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Payment</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Invoice</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allocations as $a)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $a->payment?->payment_date?->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $a->invoice?->customer?->customer_name ?? $a->payment?->customer?->customer_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-900">#{{ $a->payment_id }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $a->invoice?->invoice_number ?? $a->customer_invoice_id }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ number_format($a->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-600">No allocations yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $allocations->links() }}
            </div>

            <div id="new-allocation" class="mt-8 bg-white border border-gray-200 p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Create Allocation</h2>

                @if(($payments->count() ?? 0) === 0 || ($invoices->count() ?? 0) === 0)
                    <div class="mb-4 p-3 bg-gray-100 border border-gray-300 rounded text-gray-900">
                        @if(($payments->count() ?? 0) === 0)
                            <div>No payments found for the current business.</div>
                        @endif
                        @if(($invoices->count() ?? 0) === 0)
                            <div>No customer invoices found for the current business.</div>
                        @endif
                        <div class="text-sm text-gray-200 mt-1">Tip: switch/select a business on the dashboard, then create an invoice/payment first.</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('invoicepayments.store') }}">
                    @csrf

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12">
                            <label class="block text-sm text-black">Allocation Type</label>
                            <select id="allocation_type" name="allocation_type" class="w-64 border rounded px-3 py-2">
                                <option value="payment" @selected(old('allocation_type') == 'payment')>Payment</option>
                                <option value="credit" @selected(old('allocation_type') == 'credit')>Credit Note</option>
                            </select>
                        </div>

                        <div class="col-span-6" id="payment_select">
                            <label class="block text-sm text-black">Payment</label>
                            <select name="payment_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select payment --</option>
                                @foreach($payments as $p)
                                    <option class="text-black" value="{{ $p->id }}" @selected(old('payment_id') == $p->id)>
                                        #{{ $p->id }} — {{ $p->payment_date?->format('Y-m-d') }} — {{ $p->customer?->customer_name ?? '—' }} — {{ number_format($p->amount,2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-6" id="credit_select">
                            <label class="block text-sm text-black">Credit Note</label>
                            <select name="customer_credit_note_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select credit note --</option>
                                @foreach($creditNotes ?? [] as $cn)
                                    <option class="text-black" value="{{ $cn->id }}" @selected(old('customer_credit_note_id') == $cn->id)>
                                        #{{ $cn->id }} — {{ $cn->credit_date?->format('Y-m-d') }} — {{ $cn->customer?->customer_name ?? '—' }} — Unalloc: {{ number_format(((float)$cn->total_amount - ($cn->allocatedAmount() ?? 0)),2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-black">Invoice</label>
                            <select name="customer_invoice_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select invoice --</option>
                                @foreach($invoices as $inv)
                                    <option class="text-black" value="{{ $inv->id }}" @selected(old('customer_invoice_id') == $inv->id)>
                                        {{ $inv->invoice_number }} — {{ $inv->invoice_date?->format('Y-m-d') }} — {{ $inv->customer?->customer_name ?? '—' }} — Bal: {{ number_format($inv->balanceDue(),2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-black">Amount</label>
                            <input type="number" name="amount"  onkeypress="return event.charCode >= 48 && event.charCode <= 57" value="{{ old('amount') }}"  class="w-full border rounded px-3 py-2 text-black" placeholder="0.00">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded text-white">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('allocation_type');
    if (!type) return;

    const paymentWrap = document.getElementById('payment_select');
    const creditWrap = document.getElementById('credit_select');

    function update() {
        const v = type.value;
        if (v === 'payment') {
            if (paymentWrap) paymentWrap.style.display = '';
            if (creditWrap) creditWrap.style.display = 'none';
            // enable/disable selects
            paymentWrap?.querySelector('select')?.removeAttribute('disabled');
            creditWrap?.querySelector('select')?.setAttribute('disabled', 'disabled');
        } else if (v === 'credit') {
            if (paymentWrap) paymentWrap.style.display = 'none';
            if (creditWrap) creditWrap.style.display = '';
            paymentWrap?.querySelector('select')?.setAttribute('disabled', 'disabled');
            creditWrap?.querySelector('select')?.removeAttribute('disabled');
        }
    }

    type.addEventListener('change', update);
    update();
});
</script>
