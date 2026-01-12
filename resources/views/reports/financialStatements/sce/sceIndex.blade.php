<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Statement of Changes in Equity</h1>
                        <p class="mt-1 text-sm text-gray-600">View and manage your saved Changes in Equity reports</p>
                    </div>
                    <a href="{{ route('reports.financial.changes-in-equity') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Report
                    </a>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="get" class="flex items-center gap-3">
                    <div class="relative flex-1 max-w-md">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reports by title..." class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    @if(request('q'))
                        <a href="{{ route('reports.financial.changes-in-equity.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(($reports ?? collect()) as $report)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-equals text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $report->description ?? 'Changes in Equity Report' }}</div>
                                        <div class="text-sm text-gray-500">Changes in Equity Report</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @php
                                        $from = $report->from ? \Illuminate\Support\Carbon::parse($report->from) : null;
                                        $to = $report->to ? \Illuminate\Support\Carbon::parse($report->to) : null;
                                    @endphp
                                    {{ $from ? $from->format('M j, Y') : 'N/A' }} - {{ $to ? $to->format('M j, Y') : 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($from && $to)
                                        {{ $from->diffInDays($to) + 1 }} days
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($report->accounting_method ?? 'accrual') === 'accrual' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ ucfirst($report->accounting_method ?? 'accrual') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @php $created = $report->created_at ? \Illuminate\Support\Carbon::parse($report->created_at) : null; @endphp
                                <div>{{ $created ? $created->format('M j, Y') : 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $created ? $created->format('g:i A') : '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('reports.financial.changes-in-equity.show', $report) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors">
                                        <i class="fas fa-eye mr-1.5"></i>
                                        View Report
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-chart-line text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">No reports found</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if(request('q'))
                                            No reports match your search criteria.
                                        @else
                                            Create your first Changes in Equity report to get started.
                                        @endif
                                    </p>
                                    @if(!request('q'))
                                        <a href="{{ route('reports.financial.changes-in-equity') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <i class="fas fa-plus mr-2"></i>
                                            Create First Report
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($reports) && $reports->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} results
                        </div>
                        <div>{{ $reports->appends(request()->query())->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
