<x-app-layout>
<div class="p-4 md:p-6 lg:p-8">

    <!-- Modern Minimalist Breadcrumbs -->
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center text-sm text-gray-500 gap-2">

            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}"
                   class="text-gray-700 hover:text-blue-600 transition font-medium">
                    Dashboard
                </a>
            </li>

            <!-- Separator -->
            <li class="text-gray-400">/</li>

            <!-- Financial Reports -->
            <li>
                <a href="#" class="text-gray-700 hover:text-blue-600 transition font-medium">
                    Financial Reports
                </a>
            </li>

            <!-- Separator -->
            <li class="text-gray-400">/</li>

            <!-- Current Page -->
            <li class="text-gray-900 font-semibold">
                Profit and Loss Statement
            </li>

        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Profit and Loss Reports</h1>
                <p class="text-gray-600">Track income, expenses, and profitability over time</p>
            </div>

            @php($newRoute = Route::has('reports.financial.profit-and-loss.create')
                ? route('reports.financial.profit-and-loss.create')
                : '#'
            )

            <a href="{{ $newRoute }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl font-semibold">
                <i class="fas fa-plus-circle text-lg"></i>
                <span>Create New Report</span>
            </a>
        </div>
    </div>

    @php($items = $reports ?? collect())
    @if($items instanceof \Illuminate\Contracts\Support\Arrayable)
        @php($items = collect($items))
    @endif

    <!-- Summary Stats -->
    @if($items->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Total Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Reports</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $items->count() }}</p>
                </div>

                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <!-- Minimalist Folder Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-blue-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h5l2 2h11v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Accrual Basis -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Accrual Basis</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $items->filter(fn($r) => strtolower($r->accounting_method ?? '') === 'accrual')->count() }}
                    </p>
                </div>

                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <!-- Minimalist Trend Line Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-purple-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash Basis -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Cash Basis</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $items->filter(fn($r) => strtolower($r->accounting_method ?? '') === 'cash')->count() }}
                    </p>
                </div>

                <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center">
                    <!-- Minimalist Wallet Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 stroke-emerald-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18v10H3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12h3" />
                    </svg>
                </div>
            </div>
        </div>

    </div>
    @endif





    <!-- Empty State -->
    @if($items->count() === 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-16">
            <div class="text-center max-w-md mx-auto">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 rounded-full mb-6">
                    <i class="fas fa-file-invoice-dollar text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Reports Yet</h3>
                <p class="text-gray-600 mb-8">Get started by creating your first Profit and Loss report to track your business performance.</p>

                <a href="{{ $newRoute }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl font-semibold">
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Your First Report</span>
                </a>
            </div>
        </div>
    @else
    <!-- Search Bar -->
            <div class="p-4border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text"
                                   id="searchReports"
                                   placeholder="Search reports..."
                                    class="w-[30vw] pl-10 pr-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div><br>
        <!-- Reports List Container -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

            

            <!-- Reports Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    @foreach($items as $r)
                        @php($from = $r->date_from)
                        @php($to = $r->date_to)
                        @php($method = $r->accounting_method)
                        @php($desc = $r->description)
                        @php($id = $r->id)
                        @php($title = $r->title ?? 'Profit and Loss Report')
                        @php($editUrl = route('reports.financial.profit-and-loss.edit', $id))
                        @php($showUrl = route('reports.financial.profit-and-loss.show', $id))

                        <div class="report-card bg-white border border-gray-200 rounded-lg p-6 hover:border-blue-500 transition">

                            <!-- Card Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $title }}</h3>

                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</span>
                                        <span class="text-gray-400">→</span>
                                        <span>{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    {{ strtolower($method) === 'cash'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($method) }}
                                </span>
                            </div>

                            <!-- Description -->
                            @if($desc)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $desc }}</p>
                            @endif

                            <!-- Meta Info -->
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-4 pb-4 border-b border-gray-100">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-hashtag"></i>
                                    <span>ID: {{ $id }}</span>
                                </div>

                                @if(isset($r->created_at))
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $r->created_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3">
                                <a href="{{ $showUrl }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                                    <i class="fas fa-eye"></i>
                                    <span>View Report</span>
                                </a>

                                <a href="{{ $editUrl }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                                    <i class="">Edit</i>
                                </a>

                                <a href="{{ $showUrl }}"
                                   onclick="window.print(); return false;"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                                    <i class="fas fa-print">Print</i>
                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        <!-- Search Filter Script -->
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

    @endif

</div>
</x-app-layout>
