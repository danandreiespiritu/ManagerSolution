<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Payment</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Payment</h1>
                <div class="space-x-2">
                    <a href="{{ route('payments.edit', $payment->id) }}" class="px-3 py-1 bg-yellow-500 rounded">Edit</a>
                    <a href="{{ route('payments.index') }}" class="px-3 py-1 bg-gray-700 rounded">Back</a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-600 rounded text-white">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-600 rounded text-white">{{ session('error') }}</div>
            @endif

            <div class="bg-[#111827] p-6 rounded">
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-400">Date</dt>
                        <dd class="text-white">{{ $payment->payment_date?->format('Y-m-d') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Amount</dt>
                        <dd class="text-white">{{ number_format($payment->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Type</dt>
                        <dd class="text-white">{{ $payment->payment_type ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Customer</dt>
                        <dd class="text-white">{{ $payment->customer?->customer_name ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-sm text-gray-400">Reference</dt>
                        <dd class="text-white">{{ $payment->reference ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <h3 class="font-semibold mb-2 text-white">Allocations</h3>
                    <div class="bg-gray-900 rounded p-4">
                        @if($payment->invoicePayments->count())
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-200">Invoice</th>
                                        <th class="px-4 py-2 text-right text-gray-200">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-800">
                                    @foreach($payment->invoicePayments as $ap)
                                        <tr>
                                            <td class="px-4 py-2 text-white">
                                                {{ $ap->invoice?->invoice_number ?? $ap->customer_invoice_id }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-white">{{ number_format($ap->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-gray-400">No allocations.</div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" onsubmit="return confirm('Delete payment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 rounded text-white">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
