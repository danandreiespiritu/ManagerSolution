<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">{{ $report->title ?? 'Supplier Summary' }}</h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold mb-4">Supplier Summary</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Supplier Code</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Supplier Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Opening Balance</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Closing Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse(($data ?? []) as $row)
                                <tr>
                                    <td class="border px-4 py-2">{{ $row['supplier_code'] }}</td>
                                    <td class="border px-4 py-2">{{ $row['supplier_name'] }}</td>
                                    <td class="border px-4 py-2 text-right">{{ number_format($row['opening_balance'], 2) }}</td>
                                    <td class="border px-4 py-2 text-right">{{ number_format($row['closing_balance'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border px-4 py-4 text-center text-gray-500">No suppliers found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>