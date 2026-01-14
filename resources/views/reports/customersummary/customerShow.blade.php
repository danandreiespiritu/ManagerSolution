<x-app-layout>
    <div class="flex-1 p-6">
        <h2 class="text-xl font-semibold mb-4">Customer Summary</h2>
        <table class="min-w-full table-auto bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Customer Code</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Customer Name</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Opening Balance</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Closing Balance</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse(($data ?? []) as $row)
                    <tr>
                        <td class="border px-4 py-2">{{ $row['customer_code'] }}</td>
                        <td class="border px-4 py-2">{{ $row['customer_name'] }}</td>
                        <td class="border px-4 py-2 text-right">{{ number_format($row['opening_balance'], 2) }}</td>
                        <td class="border px-4 py-2 text-right">{{ number_format($row['closing_balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border px-4 py-4 text-center text-gray-500">No customers found for the selected period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
