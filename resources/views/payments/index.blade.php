<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
                    <p class="text-sm text-gray-600 mt-1">Customer payment records</p>
                </div>
                <a href="{{ route('payments.create') }}" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Record Payment</a>
            </div>

            <div class="bg-white border border-gray-200 rounded p-4 shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold">Date</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold">Customer</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold">Amount</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold">Allocations</th>
                            <th class="px-4 py-2 text-right text-gray-900 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($payments as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $p->payment_date?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $p->customer?->customer_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ number_format($p->amount,2) }}</td>
                                <td class="px-4 py-3 text-gray-900">
                                    @if($p->invoicePayments->count())
                                        <ul class="text-sm">
                                            @foreach($p->invoicePayments as $ap)
                                                <li>{{ $ap->invoice?->invoice_number ?? $ap->customer_invoice_id }} — {{ number_format($ap->amount,2) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('payments.show', $p->id) }}" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 border border-blue-300 text-blue-800 rounded">View</a>
                                    <a href="{{ route('payments.edit', $p->id) }}" class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200 border border-yellow-300 text-yellow-800 rounded ml-2">Edit</a>
                                    <form action="{{ route('payments.destroy', $p->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Delete payment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-100 hover:bg-red-200 border border-red-300 text-red-800 rounded">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-600">No payments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
