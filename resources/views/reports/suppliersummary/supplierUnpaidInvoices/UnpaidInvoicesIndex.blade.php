<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">
                            Supplier Statements (Unpaid Invoices)
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Saved statements for your active business
                        </p>
                    </div>

                    <a href="{{ route('reports.suppliers.statement-unpaid') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        <i class="fas fa-plus mr-2"></i>New Statement
                    </a>
                </div>

                <!-- Search Form -->
                <div class="px-6 py-4">
                    <form method="GET" class="mb-4">
                        <div class="flex gap-2 items-center">
                            <input type="text"
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Search title..."
                                   class="px-3 py-2 border border-gray-300 rounded w-64">
                            <button class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded border border-gray-300">
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left border-b">Title</th>
                                    <th class="px-3 py-2 text-left border-b">Statement Date</th>
                                    <th class="px-3 py-2 text-left border-b">Created</th>
                                    <th class="px-3 py-2 text-right border-b w-40">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr class="border-b">
                                        <td class="px-3 py-2">
                                            {{ $report->title ?? 'Supplier Statement (Unpaid Invoices)' }}
                                        </td>

                                        <td class="px-3 py-2">
                                            {{ optional($report->statement_date)->format('d/m/Y') }}
                                        </td>

                                        <td class="px-3 py-2 text-gray-600">
                                            {{ $report->created_at->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="px-3 py-2 text-right">
                                            <a href="{{ route('reports.suppliers.statement-unpaid.show', $report) }}"
                                               class="text-blue-600 hover:underline">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-3 py-6 text-center text-gray-500">
                                            No saved statements yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
