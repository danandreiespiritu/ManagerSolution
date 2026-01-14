<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Customer Invoices</h1>

                    @if(!empty($customer))
                        <p class="text-sm text-gray-600 mt-1">
                            Viewing invoices for <strong>{{ $customer->customer_name }}</strong>
                        </p>
                    @endif
                </div>

                <div class="flex gap-2">
                    <a href="#new-invoice"
                       class="px-4 py-2.5 bg-indigo-600 text-white text-sm rounded-lg shadow hover:bg-indigo-700">
                        New Invoice
                    </a>

                    <a href="{{ url()->previous() }}"
                       class="px-4 py-2.5 bg-gray-200 text-gray-800 text-sm rounded-lg shadow hover:bg-gray-300">
                        Back
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg shadow-sm">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Invoices Table -->
            <div class="bg-white border border-gray-200 rounded-xl shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Number</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Invoice Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Due Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Total</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ $inv->invoice_number }}</td>
                                <td class="px-4 py-3">{{ $inv->customer?->customer_name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ optional($inv->invoice_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ optional($inv->due_date)->format('Y-m-d') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ number_format($inv->total_amount, 2) }}</td>

                                <td class="px-4 py-3 text-right space-x-2">
                                    <a href="{{ route('customerinvoices.show', $inv->id) }}"
                                       class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 border border-blue-300 rounded text-xs hover:bg-blue-200">
                                        View
                                    </a>

                                    <a href="{{ route('customerinvoices.edit', $inv->id) }}"
                                       class="inline-block px-3 py-1.5 bg-yellow-100 text-yellow-700 border border-yellow-300 rounded text-xs hover:bg-yellow-200">
                                        Edit
                                    </a>

                                    <form action="{{ route('customerinvoices.destroy', $inv->id) }}"
                                          class="inline-block"
                                          method="POST"
                                          onsubmit="return confirm('Delete invoice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    No invoices found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- CREATE INVOICE FORM -->
            <div id="new-invoice" class="mt-10 bg-white border border-gray-200 rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Create Invoice</h2>

                <form method="POST" action="{{ route('customerinvoices.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Customer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>

                            @if(!empty($customer))
                                <div class="w-full px-3 py-2 rounded-lg border bg-gray-50 text-gray-800">
                                    {{ $customer->customer_name }}
                                </div>
                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            @else
                                <select name="customer_id"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Select customer --</option>
                                    @foreach($customers ?? [] as $c)
                                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <!-- Invoice Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number</label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Invoice Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                            <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->toDateString()) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Due Date (optional)</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Total Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                            <input type="text" name="total_amount" value="{{ old('total_amount') }}"
                                   placeholder="0.00"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status (optional)</label>
                            <input type="text" name="status" value="{{ old('status', 'Unpaid') }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                    </div>

                    <div>
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 text-sm font-medium">
                            Save Invoice
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
