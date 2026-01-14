<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>

                <div class="flex gap-2">
                    <a href="{{ route('payments.edit', $payment->id) }}"
                       class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition">
                        Edit
                    </a>

                    <a href="{{ route('payments.index') }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg shadow transition">
                        Back
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-lg p-8">

                <!-- Payment Info -->
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <dt class="text-sm text-gray-500">Payment Date</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $payment->payment_date?->format('Y-m-d') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Amount</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ number_format($payment->amount, 2) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Payment Type</dt>
                        <dd class="text-gray-900">
                            {{ $payment->payment_type ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Customer</dt>
                        <dd class="text-gray-900">
                            {{ $payment->customer?->customer_name ?? '—' }}
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Reference</dt>
                        <dd class="text-gray-900">
                            {{ $payment->reference ?? '—' }}
                        </dd>
                    </div>

                </dl>

                <!-- Allocations -->
                <div class="mt-10">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Allocations</h3>

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">

                        @if($payment->invoicePayments->count())
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Invoice</th>
                                        <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Amount</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($payment->invoicePayments as $ap)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-4 py-2 text-gray-800">
                                                {{ $ap->invoice?->invoice_number ?? $ap->customer_invoice_id }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-gray-800">
                                                {{ number_format($ap->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="py-3 text-gray-500">No allocations recorded for this payment.</p>
                        @endif

                    </div>
                </div>

                <!-- Delete Button -->
                <div class="mt-8 flex justify-end">
                    <form action="{{ route('payments.destroy', $payment->id) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this payment?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow transition">
                            Delete Payment
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
