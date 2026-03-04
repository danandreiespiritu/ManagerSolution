<x-app-layout>
    @php
        $fmt = function ($value) {
            $value = (float) $value;
            if (abs($value) < 0.005) {
                return '—';
            }
            return $value < 0
                ? '(' . number_format(abs($value), 2) . ')'
                : number_format($value, 2);
        };

        $mainLabel = $columnLabel ?: (\Carbon\Carbon::parse($from)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d M Y'));

        $columns = collect([
            ['key' => 'main', 'label' => $mainLabel, 'data' => $main],
        ])->merge(collect($comparatives ?? [])->map(function ($c) {
            return [
                'key' => 'comp',
                'label' => $c['label'] ?? 'Comparative',
                'data' => $c['result'] ?? [],
            ];
        }));

        $colCount = $columns->count();
        $sectionsMeta = [
            'operating' => 'Cash Flows from Operating Activities',
            'investing' => 'Cash Flows from Investing Activities',
            'financing' => 'Cash Flows from Financing Activities',
        ];
    @endphp

    <div class="min-h-screen p-4 md:p-6 lg:p-8 bg-gray-50">
        <div class="max-w-6xl mx-auto bg-white border border-gray-200 rounded-lg shadow">
            <div class="p-6 md:p-8 border-b border-gray-200 text-center">
                <div class="text-sm text-gray-700 font-medium">{{ $business->name ?? '' }}</div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mt-1">Statement of Cash Flows</h1>
                <div class="text-gray-600 mt-2">For the period {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
                <div class="text-xs text-gray-500 mt-1 capitalize">{{ $method }} Method</div>
                @if(!empty($description))
                    <div class="text-sm text-gray-600 mt-2">{{ $description }}</div>
                @endif

                <div class="mt-5 flex flex-wrap justify-center gap-2">
                    @if(!empty($reportId))
                        <a href="{{ route('reports.financial.cashflow.exportSaved', $reportId) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">Export CSV</a>
                    @else
                        <a href="{{ route('reports.financial.cashflow.export', request()->query()) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">Export CSV</a>
                    @endif
                    <a href="{{ route('reports.financial.cashflow.index') }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50">Back to Reports</a>
                </div>
            </div>
            @php
                // Balance validation for main period: Net Increase == Operating + Investing + Financing (tolerance)
                $mainOperating = (float) data_get($main, 'totals.operating', 0);
                $mainInvesting = (float) data_get($main, 'totals.investing', 0);
                $mainFinancing = (float) data_get($main, 'totals.financing', 0);
                $mainNetIncrease = (float) data_get($main, 'netIncrease', 0);
                $balanceTolerance = 0.01;
                $isBalancedMain = abs(($mainOperating + $mainInvesting + $mainFinancing) - $mainNetIncrease) <= $balanceTolerance;
            @endphp

            <div class="p-4 md:p-6">
                @if($isBalancedMain)
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded text-sm">Cash Flow Statement is Balanced.</div>
                @else
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded text-sm">Cash Flow Statement is NOT Balanced. Please review entries.</div>
                @endif
            </div>

            <div class="p-6 md:p-8">
                <div class="overflow-x-auto">
                    <div class="grid gap-2 text-sm font-semibold text-gray-700" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                        <div class="px-3 py-2 bg-gray-100 rounded-l">Particulars</div>
                        @foreach($columns as $col)
                            <div class="px-3 py-2 bg-gray-100 text-right {{ $loop->last ? 'rounded-r' : '' }}">{{ $col['label'] }}</div>
                        @endforeach
                    </div>
                </div>

                @foreach($sectionsMeta as $secKey => $secTitle)
                    @php
                        $allNames = collect();
                        foreach ($columns as $col) {
                            $lines = collect(data_get($col['data'], 'sections.' . $secKey . '.lines', []));
                            foreach ($lines as $line) {
                                $allNames->push($line['name'] ?? 'Unclassified');
                            }
                        }
                        $allNames = $allNames->filter()->unique()->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();
                    @endphp

                    <div class="mt-6 border-l-4 border-blue-600 bg-blue-50 px-4 py-2 font-semibold text-gray-800">{{ $secTitle }}</div>

                    @if($allNames->isEmpty())
                        <div class="text-sm text-gray-500 italic px-3 py-2">No activity for this section.</div>
                    @else
                        @foreach($allNames as $name)
                            <div class="grid gap-2 text-sm py-1" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                                <div class="px-3 text-gray-800">{{ $name }}</div>
                                @foreach($columns as $col)
                                    @php
                                        $lines = collect(data_get($col['data'], 'sections.' . $secKey . '.lines', []));
                                        $amount = (float) (optional($lines->firstWhere('name', $name))['amount'] ?? 0);
                                    @endphp
                                    <div class="px-3 text-right {{ $amount < 0 ? 'text-red-700' : 'text-gray-900' }}">{{ $fmt($amount) }}</div>
                                @endforeach
                            </div>
                        @endforeach
                    @endif

                    <div class="mt-2">
                        <div class="grid gap-2 text-sm py-1 border-t" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                            <div class="px-3 font-medium text-gray-700">Subtotal Cash Inflows</div>
                            @foreach($columns as $col)
                                @php
                                    $lines = collect(data_get($col['data'], 'sections.' . $secKey . '.lines', []));
                                    $inflows = (float) $lines->sum(function ($line) { return max(0, (float) ($line['amount'] ?? 0)); });
                                @endphp
                                <div class="px-3 text-right font-medium text-gray-700">{{ $fmt($inflows) }}</div>
                            @endforeach
                        </div>

                        <div class="grid gap-2 text-sm py-1" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                            <div class="px-3 font-medium text-gray-700">Subtotal Cash Outflows</div>
                            @foreach($columns as $col)
                                @php
                                    $lines = collect(data_get($col['data'], 'sections.' . $secKey . '.lines', []));
                                    $outflows = (float) $lines->sum(function ($line) {
                                        $amount = (float) ($line['amount'] ?? 0);
                                        return $amount < 0 ? abs($amount) : 0;
                                    });
                                @endphp
                                <div class="px-3 text-right font-medium text-gray-700">{{ $fmt(-1 * $outflows) }}</div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-2 text-sm font-semibold py-2 border-t border-b bg-gray-50 mt-1" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                        <div class="px-3">Net Cash {{ ($secKey === 'investing') ? 'Used In' : 'Provided By' }} {{ str_replace('Cash Flows from ', '', $secTitle) }}</div>
                        @foreach($columns as $col)
                            @php $net = (float) data_get($col['data'], 'totals.' . $secKey, 0); @endphp
                            <div class="px-3 text-right {{ $net < 0 ? 'text-red-700' : 'text-gray-900' }}">{{ $fmt($net) }}</div>
                        @endforeach
                    </div>
                @endforeach

                <div class="mt-8 space-y-1">
                    <div class="grid gap-2 text-sm font-bold py-2 border-t-2 border-b-2 border-gray-700 bg-yellow-50" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                        <div class="px-3">Net Increase (Decrease) in Cash</div>
                        @foreach($columns as $col)
                            @php $netIncrease = (float) data_get($col['data'], 'netIncrease', 0); @endphp
                            <div class="px-3 text-right {{ $netIncrease < 0 ? 'text-red-700' : 'text-gray-900' }}">{{ $fmt($netIncrease) }}</div>
                        @endforeach
                    </div>

                    <div class="grid gap-2 text-sm py-2" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                        <div class="px-3 font-medium text-gray-800">Cash Balance, Beginning</div>
                        @foreach($columns as $col)
                            <div class="px-3 text-right font-medium text-gray-800">{{ $fmt((float) data_get($col['data'], 'cashBeginning', 0)) }}</div>
                        @endforeach
                    </div>

                    <div class="grid gap-2 text-sm font-bold py-2 border-t-2 border-b-2 border-gray-700 bg-yellow-100" style="grid-template-columns: minmax(260px, 2fr) repeat({{ $colCount }}, minmax(130px, 1fr));">
                        <div class="px-3">Cash Balance, Ending</div>
                        @foreach($columns as $col)
                            <div class="px-3 text-right">{{ $fmt((float) data_get($col['data'], 'cashEnding', 0)) }}</div>
                        @endforeach
                    </div>
                </div>

                @php
                    $endingDiff = (float) data_get($main, 'endingDifference', 0);
                @endphp
                @if(abs($endingDiff) > 0.01)
                    <div class="mt-4 p-3 border border-yellow-200 bg-yellow-50 text-yellow-800 text-sm rounded">
                        Reconciliation notice: Actual ledger ending cash ({{ $fmt((float) data_get($main, 'actualCashEnding', 0)) }}) differs from formula-based ending cash by {{ $fmt($endingDiff) }}.
                    </div>
                @endif
            </div>

            @if(!empty($footer))
                <div class="p-6 md:p-8 border-t border-gray-200 bg-gray-50 text-sm text-gray-700 whitespace-pre-line">{{ $footer }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
