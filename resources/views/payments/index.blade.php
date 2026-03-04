<x-app-layout>
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Payments</h1>
                    <p class="text-sm text-gray-600 mt-1">View and manage customer payment records</p>
                </div>

                <a href="{{ route('payments.create') }}"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
                    Record Payment
                </a>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="bg-white rounded-xl border border-gray-200 shadow overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                            <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Customer</th>
                            <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                            <th class="px-5 py-3 text-left text-sm font-semibold text-gray-700">Allocations</th>
                            <th class="px-5 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($payments as $p)
                            <tr class="hover:bg-gray-50 transition">

                                <!-- DATE -->
                                <td class="px-5 py-3 text-gray-800">
                                    {{ $p->payment_date?->format('Y-m-d') }}
                                </td>

                                <!-- CUSTOMER -->
                                <td class="px-5 py-3 text-gray-800">
                                    {{ $p->customer?->customer_name ?? '-' }}
                                </td>

                                <!-- AMOUNT -->
                                <td class="px-5 py-3 font-medium text-gray-900">
                                    {{ number_format($p->amount, 2) }}
                                </td>

                                <!-- ALLOCATIONS -->
                                <td class="px-5 py-3 text-gray-700">
                                    @if($p->invoicePayments->count())
                                        <ul class="space-y-1 text-sm">
                                            @foreach($p->invoicePayments as $ap)
                                                <li class="bg-gray-100 px-2 py-1 rounded text-gray-900 border border-gray-200">
                                                    {{ $ap->invoice?->invoice_number ?? $ap->customer_invoice_id }}
                                                    — <span class="font-medium">{{ number_format($ap->amount, 2) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <!-- ACTIONS -->
                                <td class="px-5 py-3 text-right whitespace-nowrap">

                                    <a href="{{ route('payments.show', $p->id) }}"
                                        class="inline-block px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-300 rounded-md text-sm transition">
                                        View
                                    </a>

                                    <a href="{{ route('payments.edit', $p->id) }}"
                                        class="inline-block px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 border border-yellow-300 rounded-md text-sm ml-2 transition">
                                        Edit
                                    </a>

                                    <form action="{{ route('payments.destroy', $p->id) }}" method="POST"
                                        class="inline-block ml-2"
                                        onsubmit="return confirm('Delete payment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 border border-red-300 rounded-md text-sm transition">
                                            Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-gray-500">
                                    No payments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- PAGINATION -->
                <div class="px-5 py-4 bg-gray-50 border-t">
                    {{ $payments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
