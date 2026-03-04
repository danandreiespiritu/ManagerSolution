<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">{{ $report->title }}</h1>
            <div class="text-sm text-gray-500">{{ $from->format('M j, Y') }} - {{ $to->format('M j, Y') }}</div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 space-y-6">
            <div class="bg-white border rounded-lg p-6">
                <div class="mt-2 text-sm text-gray-700">
                    <span class="font-medium">Customer:</span>
                    {{ $customer?->name ?? 'All customers' }}
                </div>
            </div>

            <div class="bg-white border rounded-lg p-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-3 py-2">Date</th>
                            <th class="text-left px-3 py-2">Type</th>
                            <th class="text-left px-3 py-2">Reference</th>
                            <th class="text-left px-3 py-2">Description</th>
                            <th class="text-right px-3 py-2">Debit</th>
                            <th class="text-right px-3 py-2">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($entries as $e)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ optional($e->date)->format('M j, Y') }}</td>
                                <td class="px-3 py-2">{{ $e->type }}</td>
                                <td class="px-3 py-2">{{ $e->reference }}</td>
                                <td class="px-3 py-2">{{ $e->description }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format((float)($e->debit ?? 0), 2) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format((float)($e->credit ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-gray-500">No transactions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-right">Totals</td>
                            <td class="px-3 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-right">Balance</td>
                            <td colspan="2" class="px-3 py-2 text-right">{{ number_format($balance, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
