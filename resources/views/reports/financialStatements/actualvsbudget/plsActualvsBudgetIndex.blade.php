<x-app-layout>
<div class="p-4 md:p-6 lg:p-8 bg-gray-50 min-h-screen">

    <!-- Breadcrumbs -->
    <nav class="mb-6">
        <ol class="flex items-center text-sm text-gray-500 gap-2">
            <li><a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Financial Reports</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900 font-semibold">P&L (Actual vs Budget)</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Profit and Loss (Actual vs Budget)</h1>
            <p class="text-gray-600">Compare actual performance vs planned budget across periods</p>
        </div>

        <a href="{{ route('reports.financial.profit-and-loss.actual-and-budget.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-white" fill="none" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create New Report</span>
        </a>
    </div>

    @php($items = $reports ?? collect())


    <!-- Summary Cards -->
    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Total Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Reports</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" id="totalCount">{{ $items->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Accrual -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Accrual Basis</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" id="accrualCount">
                        {{ $items->where('accounting_method', 'accrual')->count() }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-purple-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M3 17l6-6 4 4 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Cash Basis</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" id="cashCount">
                        {{ $items->where('accounting_method', 'cash')->count() }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M3 7h18v10H3z" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 12h3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>
    @endif

    <!-- Search Input -->
    <div class="mb-6">
        <div class="relative max-w-sm">
            <input type="text" id="searchReports" placeholder="Search reports..."
                   class="w-full pl-10 pr-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
        </div>
    </div>

    <!-- Reports Card Grid -->
    <div id="reportGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        @foreach($items as $r)
        @php($show = route('reports.financial.profit-and-loss.actual-and-budget.show', $r->id))
        @php($edit = route('reports.financial.profit-and-loss.actual-and-budget.edit', $r->id))

        <div class="report-card bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 transition shadow-sm"
             data-title="{{ strtolower($r->title) }}"
             data-method="{{ strtolower($r->accounting_method) }}"
             data-date="{{ $r->date_from }} {{ $r->date_to }}">

            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $r->title }}</h3>

                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                            <path d="M8 7h8v10H8z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ optional($r->date_from)->format('d M Y') }}</span>
                        <span class="text-gray-400">→</span>
                        <span>{{ optional($r->date_to)->format('d M Y') }}</span>
                    </div>
                </div>

                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $r->accounting_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($r->accounting_method) }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ $show }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    View Report
                </a>
                <a href="{{ $edit }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Edit
                </a>
            </div>
        </div>
        @endforeach

    </div>

</div>


<!-- AJAX Search Script -->
<script>
document.getElementById('searchReports').addEventListener('input', function () {
    let term = this.value.toLowerCase();

    let cards = document.querySelectorAll('#reportGrid .report-card');

    cards.forEach(card => {
        let matches =
            card.dataset.title.includes(term) ||
            card.dataset.method.includes(term) ||
            card.dataset.date.includes(term);

        card.style.display = matches ? '' : 'none';
        card.style.opacity = matches ? 1 : 0;
    });
});
</script>

</x-app-layout>
