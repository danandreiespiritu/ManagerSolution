<x-app-layout>
<div class="p-6 md:p-8 bg-gray-50">

    <!-- Breadcrumbs -->
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center gap-2 text-sm text-gray-500">

            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 font-medium hover:text-blue-600">Dashboard</a>
            </li>

            <li class="text-gray-400">/</li>

            <li>
                <span class="text-gray-700 font-medium">Financial Reports</span>
            </li>

            <li class="text-gray-400">/</li>

            <li class="text-gray-900 font-semibold">Profit & Loss Reports</li>

        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Profit & Loss Reports</h1>
            <p class="text-gray-600 mt-1">Analyze profitability across custom date ranges</p>
        </div>

        <a href="{{ route('reports.financial.profit-and-loss.create') }}"
           class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 hover:shadow-md transition font-medium">
            <i class="fas fa-plus-circle"></i>
            Create Report
        </a>
    </div>

    @php($items = collect($reports ?? []))

    <!-- Stats Summary -->
    @if($items->count())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <!-- Total Reports -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Total Reports</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $items->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-700 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Accrual -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Accrual Reports</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $items->where('accounting_method', 'accrual')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-700 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Cash -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Cash Reports</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $items->where('accounting_method', 'cash')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-emerald-700 text-xl"></i>
                </div>
            </div>
        </div>

    </div>
    @endif

    <!-- Empty State -->
    @if(!$items->count())
    <div class="bg-white p-12 rounded-xl border border-gray-200 shadow-sm text-center max-w-lg mx-auto">
        <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full mx-auto mb-6 flex items-center justify-center">
            <i class="fas fa-file-invoice-dollar text-4xl"></i>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">No Reports Found</h2>
        <p class="text-gray-600 mb-6">Create your first P&L report to begin analyzing performance.</p>

        <a href="{{ route('reports.financial.profit-and-loss.create') }}"
           class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 hover:shadow-md transition font-medium">
            <i class="fas fa-plus-circle"></i> Create Report
        </a>
    </div>
    @else

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative w-full md:w-96">
            <input type="text"
                   id="searchReports"
                   placeholder="Search reports..."
                   class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
    </div>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        @foreach($items as $r)
        <div class="report-card bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-blue-400 transition">

            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $r->title }}</h3>

                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ optional($r->date_from)->format('d M Y') }}</span>
                        <span class="text-gray-400">→</span>
                        <span>{{ optional($r->date_to)->format('d M Y') }}</span>
                    </div>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $r->accounting_method === 'cash'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($r->accounting_method) }}
                </span>
            </div>

            @if($r->description)
                <p class="text-gray-600 text-sm mt-3 line-clamp-2">{{ $r->description }}</p>
            @endif

            <!-- Metadata -->
            <div class="border-t border-gray-100 mt-4 pt-4 text-xs text-gray-500 flex items-center gap-4">
                <span><i class="fas fa-hashtag"></i> ID: {{ $r->id }}</span>
                <span><i class="fas fa-clock"></i> {{ $r->created_at->diffForHumans() }}</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-5">

                <a href="{{ route('reports.financial.profit-and-loss.show', $r->id) }}"
                   class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-center font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-eye"></i> View
                </a>

                <a href="{{ route('reports.financial.profit-and-loss.edit', $r->id) }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-edit"></i>
                </a>
            </div>

        </div>
        @endforeach

    </div>

    @endif

</div>

<!-- Search Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('searchReports');
        const cards = document.querySelectorAll('.report-card');

        input.addEventListener('input', e => {
            const term = e.target.value.toLowerCase();

            cards.forEach(card => {
                const match = card.textContent.toLowerCase().includes(term);
                card.style.display = match ? '' : 'none';
            });
        });
    });
</script>

</x-app-layout>
