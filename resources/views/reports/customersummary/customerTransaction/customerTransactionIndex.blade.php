<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold">Customer Statements (Transactions)</h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Customer Statements (Transactions)</h2>
                    <a href="{{ route('reports.customers.statement-transactions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        <i class="fas fa-plus mr-2"></i> New Statement
                    </a>
                </div>
                <div class="p-6">
                    @if(isset($reports) && $reports->count())
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-3 py-2">Title</th>
                                    <th class="text-left px-3 py-2">From</th>
                                    <th class="text-left px-3 py-2">To</th>
                                    <th class="text-left px-3 py-2">Created</th>
                                    <th class="text-left px-3 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($reports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $report->title }}</td>
                                        <td class="px-3 py-2">{{ optional($report->from_date)->format('M j, Y') }}</td>
                                        <td class="px-3 py-2">{{ optional($report->to_date)->format('M j, Y') }}</td>
                                        <td class="px-3 py-2">{{ $report->created_at->format('M j, Y g:i A') }}</td>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('reports.customers.statement-transactions.show', $report) }}" class="text-blue-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $reports->links() }}</div>
                    @else
                        <div class="text-center text-gray-500 py-10">No statements yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
