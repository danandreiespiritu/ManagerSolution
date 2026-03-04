<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Supplier Statement (Transactions)</h1>
            <div class="text-sm text-gray-500">{{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Statement</h2>
                        @if($supplier)
                            <div class="mt-2">
                                <div class="font-semibold">{{ $supplier->name }}</div>
                                <div class="text-gray-600">{{ $supplier->address ?? '' }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left border-b">Date</th>
                            <th class="px-3 py-2 text-left border-b">Type</th>
                            <th class="px-3 py-2 text-left border-b">Reference</th>
                            <th class="px-3 py-2 text-left border-b">Description</th>
                            <th class="px-3 py-2 text-right border-b">Debit</th>
                            <th class="px-3 py-2 text-right border-b">Credit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($entries as $e)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ optional($e->date)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2">{{ $e->type }}</td>
                                <td class="px-3 py-2">{{ $e->reference }}</td>
                                <td class="px-3 py-2">{{ $e->description }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format((float)($e->debit ?? 0), 2) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format((float)($e->credit ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-gray-500">No transactions in this period.</td>
                            </tr>
                        @endforelse
                        </tbody>
                        <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-right">Totals</td>
                            <td class="px-3 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-3 py-2 text-right font-semibold">Balance</td>
                            <td colspan="2" class="px-3 py-2 text-right font-semibold">{{ number_format($balance, 2) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>