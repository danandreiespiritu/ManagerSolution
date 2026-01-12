<x-app-layout>
<div class="max-w-6xl mx-auto px-5 py-6 text-gray-900">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('reports.financialStatements.profit-and-loss.actual-and-budget.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>

    <!-- Header Section -->
    <div class="text-center mb-8">
        <div class="text-gray-700 font-medium">{{ $businessName }}</div>

        <h1 class="text-3xl font-bold text-gray-900 mt-1">
            Profit and Loss Statement (Actual vs Budget)
        </h1>

        <p class="text-gray-600 mt-2 text-base">
            From <span class="font-semibold text-gray-900">{{ $report->date_from->format('d/m/Y') }}</span>
            to <span class="font-semibold text-gray-900">{{ $report->date_to->format('d/m/Y') }}</span>
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <!-- Column Headers -->
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 text-sm font-medium text-gray-600 grid grid-cols-12 gap-2">
            <div class="col-span-6"></div>
            <div class="col-span-2 text-right">Actual</div>
            <div class="col-span-2 text-right">Budget</div>
            <div class="col-span-1 text-right">% Diff</div>
            <div class="col-span-1 text-right">Remaining</div>
        </div>

        @php
            $groups = $grouped ?? [];
            $fmt = fn($v) => $v === null || abs($v) < 0.005
                ? '—'
                : ($v < 0 ? '(' . number_format(abs($v),2) . ')' : number_format($v,2));

            $fmtPct = fn($v) => $v === null || abs($v) < 0.005
                ? '—'
                : number_format($v, 2) . '%';
        @endphp

        <div class="divide-y">
        @forelse($groups as $groupName => $g)
            <div class="p-5">

                <!-- Group Header -->
                <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ $groupName }}</h2>

                <!-- Accounts List -->
                <div class="space-y-2">
                    @foreach($g['accounts'] as $row)
                    <div class="grid grid-cols-12 gap-2 text-sm items-center">
                        <div class="col-span-6 text-gray-800 truncate">
                            {{ $row['name'] }}
                        </div>

                        <div class="col-span-2 text-right font-medium text-blue-700">
                            {{ $fmt($row['actual']) }}
                        </div>

                        <div class="col-span-2 text-right text-gray-800">
                            {{ $fmt($row['budget']) }}
                        </div>

                        <div class="col-span-1 text-right">
                            {{ $fmtPct($row['percentage']) }}
                        </div>

                        <div class="col-span-1 text-right">
                            {{ $fmt($row['remaining']) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Group Totals -->
                <div class="mt-4 pt-3 border-t border-gray-200 grid grid-cols-12 gap-2 text-sm font-semibold text-gray-900">
                    <div class="col-span-6">Total — {{ $groupName }}</div>
                    <div class="col-span-2 text-right">{{ $fmt($g['totals']['actual'] ?? 0) }}</div>
                    <div class="col-span-2 text-right">{{ $fmt($g['totals']['budget'] ?? 0) }}</div>
                    <div class="col-span-1 text-right">{{ $fmtPct($g['totals']['percentage'] ?? null) }}</div>
                    <div class="col-span-1 text-right">{{ $fmt($g['totals']['remaining'] ?? 0) }}</div>
                </div>

            </div>

            <!-- NET PROFIT / LOSS SECTION -->
            @if($groupName === 'Expenses')
                @php
                    $overallActual = $overallBudget = $overallRemaining = 0;
                    foreach ($groups as $group) {
                        $overallActual += ($group['totals']['actual'] ?? 0);
                        $overallBudget += ($group['totals']['budget'] ?? 0);
                        $overallRemaining += ($group['totals']['remaining'] ?? 0);
                    }
                    $overallPct = $overallBudget != 0 ? ($overallActual / $overallBudget) * 100 : null;
                @endphp

                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="grid grid-cols-12 gap-2 text-sm font-bold text-gray-900">
                        <div class="col-span-6">Net Profit / (Loss)</div>
                        <div class="col-span-2 text-right">{{ $fmt($overallActual) }}</div>
                        <div class="col-span-2 text-right">{{ $fmt($overallBudget) }}</div>
                        <div class="col-span-1 text-right">{{ $fmtPct($overallPct) }}</div>
                        <div class="col-span-1 text-right">{{ $fmt($overallRemaining) }}</div>
                    </div>
                </div>
            @endif

        @empty
            <div class="p-6 text-center text-gray-500">No data available for this period.</div>
        @endforelse
        </div>

        @if($report->footer)
        <div class="p-5 border-t text-sm text-gray-700 whitespace-pre-line">
            {{ $report->footer }}
        </div>
        @endif

    </div>
</div>
</x-app-layout>
