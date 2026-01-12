<x-app-layout>
    <style>
        html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif; }
        .report-card { transition: all 0.2s ease; }
        .report-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .table-row:hover { background-color: #f9fafb; }
        .action-btn { transition: all 0.2s ease; } .action-btn:hover { transform: scale(1.05); }
    </style>

    <main class="flex-1 p-4 md:p-6 lg:p-8">
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900"><i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>Cash Flow Statement Reports</h1>
                    <p class="text-sm md:text-base text-gray-600 mt-2">Manage and view your cash flow statements</p>
                </div>
                <div>
                    <a href="{{ route('reports.financial.cashflow.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 shadow-md transition-all" aria-label="Create New Report"><i class="fas fa-plus-circle"></i><span>New Report</span></a>
                </div>
            </div>
        </div>

        @if($reports && $reports->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-5 report-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Total Reports</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $reports->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center"><i class="fas fa-file-alt text-blue-600 text-xl"></i></div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 report-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Latest Report</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ optional($reports->first()->to)->format('M Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center"><i class="fas fa-calendar-check text-emerald-600 text-xl"></i></div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-5 report-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Methods Used</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1 capitalize">{{ $reports->pluck('method')->unique()->implode(', ') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center"><i class="fas fa-chart-line text-purple-600 text-xl"></i></div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h2 class="text-lg font-semibold text-gray-800">All Reports</h2>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search reports..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-all" aria-label="Search reports" />
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-gray-700 font-semibold">
                            <th scope="col" class="px-4 py-3 w-40">Actions</th>
                            <th scope="col" class="px-4 py-3">Method</th>
                            <th scope="col" class="px-4 py-3">From Date</th>
                            <th scope="col" class="px-4 py-3">To Date</th>
                            <th scope="col" class="px-4 py-3">Description</th>
                            <th scope="col" class="px-4 py-3">Created</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-gray-200 bg-white">
                        @forelse($reports ?? [] as $report)
                        <tr class="table-row" data-search="{{ strtolower($report->method . ' ' . ($report->description ?? '')) }}">
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('reports.financial.cashflow.showSaved', $report->id) }}" class="action-btn inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 shadow-sm" title="View Report" aria-label="View Report"><i class="fas fa-eye mr-1"></i>View</a>
                                    <a href="{{ route('reports.financial.cashflow.editSaved', $report->id) }}" class="action-btn inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 shadow-sm" title="Edit Report" aria-label="Edit Report"><i class="fas fa-edit mr-1"></i>Edit</a>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium capitalize {{ $report->method === 'indirect' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}"><i class="fas fa-{{ $report->method === 'indirect' ? 'layer-group' : 'stream' }} mr-1"></i>{{ $report->method }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 font-medium">{{ optional($report->from)->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-900 font-medium">{{ optional($report->to)->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ Str::limit($report->description ?? 'No description', 50) }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $report->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Reports Yet</h3>
                                    <p class="text-gray-500 mb-4">Create your first Cash Flow Statement to get started.</p>
                                    <a href="{{ route('reports.financial.cashflow.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-all"><i class="fas fa-plus"></i>Create Report</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('reportTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr[data-search]');
                rows.forEach(row => { const searchData = row.getAttribute('data-search'); row.style.display = searchData.includes(searchTerm) ? '' : 'none'; });
            });
        }
    });
    </script>
</x-app-layout>