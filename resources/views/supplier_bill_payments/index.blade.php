<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Supplier Bill Payment Allocations
        </h2>
    </x-slot>

    <div class="py-6 mt-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Supplier Bill Payment Allocations
                </h1>
           
            </div>

            {{-- Success/Error messages --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
                    {{ session('error') }}
                </div>
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

            {{-- TABLE --}}
            <div class="bg-white border border-gray-200 rounded shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 text-sm font-semibold">Date</th>
                            <th class="px-4 py-3 text-left text-gray-700 text-sm font-semibold">Supplier</th>
                            <th class="px-4 py-3 text-left text-gray-700 text-sm font-semibold">Payment</th>
                            <th class="px-4 py-3 text-left text-gray-700 text-sm font-semibold">Bill</th>
                            <th class="px-4 py-3 text-left text-gray-700 text-sm font-semibold">Amount</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allocations as $a)
                            <tr class="hover:bg-gray-50 text-gray-900">
                                <td class="px-4 py-3">
                                    {{ $a->payment?->payment_date?->format('Y-m-d') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $a->bill?->supplier?->supplier_name ?? $a->payment?->supplier?->supplier_name ?? '—' }}
                                </td>
                                <td class="px-4 py-3">#{{ $a->supplier_payment_id }}</td>
                                <td class="px-4 py-3">
                                    {{ $a->bill?->bill_number ?? $a->supplier_bill_id }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ number_format($a->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-600">
                                    No allocations found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $allocations->links() }}
            </div>

            {{-- CREATE FORM --}}
            <div id="new-allocation"
                 class="mt-8 bg-white border border-gray-200 p-6 rounded shadow">

                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Create Allocation
                </h2>

                {{-- Notify no bills or payments --}}
                @if(($payments->count() ?? 0) === 0 || ($bills->count() ?? 0) === 0)
                    <div class="mb-4 p-3 bg-yellow-100 border border-yellow-300 rounded text-yellow-800">
                        @if(($payments->count() ?? 0) === 0)
                            <div>No supplier payments found for the current business.</div>
                        @endif

                        @if(($bills->count() ?? 0) === 0)
                            <div>No supplier bills found for the current business.</div>
                        @endif

                        <div class="text-sm mt-1">
                            Tip: switch/select a business on the dashboard, then create a bill/payment first.
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('supplierbillpayments.store') }}">
                    @csrf

                    <div class="grid grid-cols-12 gap-4">

                        {{-- Payment Select --}}
                        <div class="col-span-6">
                            <label class="block text-sm text-gray-700 font-medium">
                                Supplier Payment
                            </label>
                            <select name="supplier_payment_id"
                                    class="w-full border border-gray-300 rounded px-3 py-2 bg-white text-gray-900">
                                <option value="">-- Select payment --</option>
                                @foreach($payments as $p)
                                    <option value="{{ $p->id }}" @selected(old('supplier_payment_id') == $p->id)>
                                        #{{ $p->id }} — {{ $p->payment_date?->format('Y-m-d') }} —
                                        {{ $p->supplier?->supplier_name ?? '—' }} —
                                        Unalloc: {{ number_format($p->unallocatedAmount(),2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bill Select --}}
                        <div class="col-span-6">
                            <label class="block text-sm text-gray-700 font-medium">
                                Supplier Bill
                            </label>
                            <select name="supplier_bill_id"
                                    class="w-full border border-gray-300 rounded px-3 py-2 bg-white text-gray-900">
                                <option value="">-- Select bill --</option>
                                @foreach($bills as $b)
                                    <option value="{{ $b->id }}" @selected(old('supplier_bill_id') == $b->id)>
                                        {{ $b->bill_number }} — {{ $b->bill_date?->format('Y-m-d') }} —
                                        {{ $b->supplier?->supplier_name ?? '—' }} —
                                        Bal: {{ number_format($b->balanceDue(),2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Amount --}}
                        <div class="col-span-6">
                            <label class="block text-sm text-gray-700 font-medium">
                                Amount
                            </label>
                            <input type="text"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   class="w-full border border-gray-300 rounded px-3 py-2 bg-white text-gray-900"
                                   placeholder="0.00">
                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                            Save
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
