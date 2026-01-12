<x-app-layout>
<div class="p-4 md:p-6 lg:p-8 bg-gray-50 min-h-screen">

    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center text-sm text-gray-500 gap-2">
            <li><a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><span class="text-gray-700 font-medium">Financial Reports</span></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900 font-semibold">Balance Sheet</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Balance Sheet Reports</h1>
            <p class="text-gray-600">View, compare, and manage your financial position</p>
        </div>

        <a href="{{ route('reports.financial.balance-sheet.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg hover:shadow-xl font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>New Report</span>
        </a>
    </div>

    @php
        $items = $reports ?? collect();
        if ($items instanceof \Illuminate\Contracts\Support\Arrayable) {
            $items = collect($items);
        }
    @endphp



    <!-- Summary Cards -->
    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Total Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Reports</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" id="totalCount">
                        {{ $items->count() }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M6 2h9l5 5v13a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash Basis -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Cash Basis</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" id="cashCount">
                        {{ $items->where('accounting_method', 'cash')->count() }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-amber-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M3 7h18v10H3z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Accrual Basis -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Accrual Basis</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" id="accrualCount">
                        {{ $items->where('accounting_method', 'accrual')->count() }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-green-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path d="M3 17l6-6 4 4 8-8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>
    @endif
    <!-- Search Input -->
    <div class="mb-6">
        <div class="relative max-w-sm">
            <input type="text"
                   id="searchReports"
                   placeholder="Search reports..."
                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-gray-900 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 stroke-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                 fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                <circle cx="11" cy="11" r="8" />
                <path d="M21 21l-4.35-4.35" />
            </svg>
        </div>
    </div>

    <!-- Reports Grid -->
    <div id="reportGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($items as $r)
        @php
            $layoutMap = [
                'assets-minus-liabilities-equals-equity' => 'Assets - Liabilities = Equity',
                'assets-equals-liabilities-plus-equity' => 'Assets = Liabilities + Equity',
                'assets-equals-equity-plus-liabilities' => 'Assets = Equity + Liabilities',
            ];
            $layout = $layoutMap[$r->layout] ?? $r->layout;
            $method = strtolower($r->accounting_method) === 'cash' ? 'Cash Basis' : 'Accrual Basis';
        @endphp

        <div class="report-card bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 transition shadow-sm"
             data-title="{{ strtolower($r->title) }}"
             data-method="{{ strtolower($r->accounting_method) }}"
             data-layout="{{ strtolower($layout) }}"
             data-date="{{ $r->date }}">

            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $r->title }}</h3>

                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                            <path d="M8 7h8v10H8z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</span>
                    </div>
                </div>

                <span class="px-3 py-1 text-xs font-semibold rounded-full
                    {{ strtolower($r->accounting_method) === 'cash' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                    {{ $method }}
                </span>
            </div>

            <p class="text-sm text-gray-600 mb-4">{{ $layout }}</p>

            @if($r->description)
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ $r->description }}
            </p>
            @endif

            <div class="flex items-center gap-3">
                <a href="{{ route('reports.financial.balance-sheet.show', $r->id) }}"
                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    View Report
                </a>

                <a href="{{ route('reports.financial.balance-sheet.create', ['edit' => $r->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                    Edit
                </a>

                <button
                    onclick="openDeleteModal({{ $r->id }}, '{{ $r->title }}')"
                    class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-medium">
                    Delete
                </button>
            </div>

        </div>
        @endforeach
    </div>

</div>


<!-- AJAX Search Script -->
<script>
document.getElementById('searchReports').addEventListener('input', function () {
    const term = this.value.toLowerCase();
    const cards = document.querySelectorAll('#reportGrid .report-card');

    cards.forEach(card => {
        const title = card.dataset.title;
        const method = card.dataset.method;
        const layout = card.dataset.layout;
        const date = card.dataset.date;

        const matches =
            title.includes(term) ||
            method.includes(term) ||
            layout.includes(term) ||
            date.includes(term);

        if (matches) {
            card.style.display = '';
            card.style.opacity = '1';
        } else {
            card.style.display = 'none';
            card.style.opacity = '0';
        }
    });
});
</script>

</x-app-layout>
