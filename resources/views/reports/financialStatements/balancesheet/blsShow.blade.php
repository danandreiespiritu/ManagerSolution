<x-app-layout>
    <div class="min-h-screen bg-gray-50 p-6">

        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('reports.financial.balance-sheet.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span class="text-sm font-medium">Back to List</span>
                </a>
            </div>

            <!-- Header -->
            <div class="text-center mb-4">
                <div class="text-sm text-gray-600">{{ $business->name ?? '' }}</div>

                <h1 class="text-3xl font-bold text-gray-900 mt-1">
                    {{ $report->title ?: 'Statement of Financial Position' }}
                </h1>

                <div class="mt-1 text-gray-700">
                    @if($report->from && $report->to)
                        From {{ \Carbon\Carbon::parse($report->from)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($report->to)->format('d/m/Y') }}
                    @else
                        As at {{ \Carbon\Carbon::parse($report->date ?? $report->to ?? now())->format('d/m/Y') }}
                    @endif
                </div>

                <div class="mt-1 flex items-center justify-center gap-2">
                    <div class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold tracking-wide">
                        {{ ucfirst($report->accounting_method) }} Basis
                    </div>
                    @if(!empty($report->equation))
                        @php
                            $eqLabel = $report->equation === 'extended'
                                ? 'ASSET = LIABILITIES + EQUITY + REVENUE - EXPENSES'
                                : 'ASSET = LIABILITIES + EQUITY';
                        @endphp
                        <div class="inline-flex px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-semibold tracking-wide">
                            {{ $eqLabel }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-gray-200 my-4"></div>

            <!-- Right aligned date / period -->
            <div class="text-right text-sm text-gray-600">
                @if($report->from && $report->to)
                    {{ \Carbon\Carbon::parse($report->from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($report->to)->format('d/m/Y') }}
                @else
                    {{ \Carbon\Carbon::parse($report->date ?? $report->to ?? now())->format('d/m/Y') }}
                @endif
            </div>

            @php
                // Column headers: main + comparatives
                $mainLabel = ($report->from && $report->to)
                    ? \Carbon\Carbon::parse($report->from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($report->to)->format('d/m/Y')
                    : \Carbon\Carbon::parse($report->date ?? $report->to ?? now())->format('d/m/Y');

                $columns = collect([['id' => 'main', 'label' => $mainLabel]])
                    ->merge($report->columns->map(fn($c) => ['id' => $c->id, 'label' => \Carbon\Carbon::parse($c->date)->format('d/m/Y')]));

                // Formatting
                $fmt = function($v){
                    $v = (float)$v;
                    if (abs($v) < 0.005) return '-';
                    return $v < 0 ? '-' . number_format(abs($v), 2) : number_format($v, 2);
                };

                $displayAmount = function($value, $secKey){
                    $value = (float) $value;
                    return $secKey === 'equity' ? (-1 * $value) : $value;
                };
            @endphp

            <!-- Sections: table-style spreadsheet layout -->
            <div class="mt-6">
                @php
                    $renderOrder = $order ?? ['assets', 'liabilities', 'equity'];
                @endphp

                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="text-left px-4 py-2 border-b">Account</th>
                                <th class="text-right px-4 py-2 border-b">{{ $mainLabel }}</th>
                                @foreach($report->columns as $col)
                                    <th class="text-right px-4 py-2 border-b">{{ \Carbon\Carbon::parse($col->date)->format('d/m/Y') }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($renderOrder as $secKey)
                            @php $section = $sections[$secKey] ?? null; @endphp
                            @if(!$section) @continue @endif

                            <!-- Section title row -->
                            <tr>
                                <td colspan="{{ 1 + max(1, $columns->count()) }}" class="px-4 py-3 font-semibold text-gray-800">{{ $section['title'] }}</td>
                            </tr>

                            @php
                                $groups = $section['groups'] ?? [];
                                // For assets & liabilities: order current groups first, then non-current, then others
                                if ($secKey === 'assets' || $secKey === 'liabilities') {
                                    $currentKeys = [];
                                    $nonCurrentKeys = [];
                                    $otherKeys = [];
                                    foreach(array_keys($groups) as $gk) {
                                        $norm = strtolower(preg_replace('/[^a-z0-9\- ]/','', (string)$gk));
                                        // Detect non-current first (handles 'non-current', 'non current', 'noncurrent')
                                        if (preg_match('/non\s*-?current/i', $gk) || strpos($norm, 'noncurrent') !== false) {
                                            $nonCurrentKeys[] = $gk;
                                        } elseif (preg_match('/\bcurrent\b/i', $gk)) {
                                            // current (but not non-current)
                                            $currentKeys[] = $gk;
                                        } else {
                                            $otherKeys[] = $gk;
                                        }
                                    }
                                    // Place current first, then non-current, then any others
                                    $orderedKeys = array_merge($currentKeys, $nonCurrentKeys, $otherKeys);
                                } else {
                                    $orderedKeys = array_keys($groups);
                                }
                            @endphp

                            @foreach($orderedKeys as $groupName)
                                @php $accounts = $groups[$groupName] ?? []; @endphp
                                <!-- Group name -->
                                <tr>
                                    @php
                                        $displayGroup = $groupName;
                                        $norm = strtolower(preg_replace('/[^a-z0-9]/','', (string)$groupName));
                                        if (strpos($norm, 'noncurrent') !== false || stripos($groupName, 'non-current') !== false || stripos($groupName, 'non current') !== false) {
                                            $displayGroup = 'Non-current Assets';
                                        }
                                    @endphp
                                    <td colspan="{{ 1 + max(1, $columns->count()) }}" class="px-4 py-1 text-gray-700 pl-6">{{ $displayGroup }}</td>
                                </tr>

                                @php
                                    $accountsList = $accounts;
                                    if ($secKey === 'equity') {
                                        $ownerCapital = null; $drawings = null; $retained = null;
                                        foreach ($accounts as $a) {
                                            $name = strtolower((string) ($a->account_name ?? ''));
                                            if (str_contains($name, 'owner') && str_contains($name, 'capital')) { $ownerCapital = $a; continue; }
                                            if (str_contains($name, 'draw') || str_contains($name, 'withdraw')) { $drawings = $a; continue; }
                                            if (str_contains($name, 'retained')) { $retained = $a; continue; }
                                        }
                                        $newOrder = [];
                                        if ($ownerCapital) $newOrder[] = $ownerCapital;
                                        if ($drawings) $newOrder[] = $drawings;
                                        if ($retained) $newOrder[] = $retained;
                                        foreach ($accounts as $a) {
                                            $found = false;
                                            foreach ($newOrder as $n) { if (($n->account_id ?? null) === ($a->account_id ?? null)) { $found = true; break; } }
                                            if (! $found) $newOrder[] = $a;
                                        }
                                        $accountsList = $newOrder;
                                    }
                                @endphp

                                @foreach($accountsList as $acc)
                                    <tr>
                                        <td class="px-4 py-1 pl-10 text-gray-900">{{ $acc->account_name ?? '' }}</td>
                                        <td class="px-4 py-1 text-right">{{ $fmt($displayAmount($acc->balance, $secKey)) }}</td>
                                        @foreach($report->columns as $col)
                                            <td class="px-4 py-1 text-right">{{ $fmt($displayAmount((float)($acc->comparatives[$col->id] ?? 0), $secKey)) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach

                            @endforeach

                            @if($secKey === 'equity')
                                <tr>
                                    <td class="px-4 py-1 pl-10 text-gray-900">Net Income</td>
                                    <td class="px-4 py-1 text-right">{{ $fmt($plMainNet ?? 0) }}</td>
                                    @foreach($report->columns as $col)
                                        @php $plNet = (float) ($sectionComparativeTotals[$col->id]['pl_net'] ?? 0); @endphp
                                        <td class="px-4 py-1 text-right">{{ $fmt($plNet) }}</td>
                                    @endforeach
                                </tr>
                            @endif

                            <!-- Section total -->
                            <tr class="border-t">
                                <td class="px-4 py-2 font-semibold pl-4">Total — {{ $section['title'] }}</td>
                                <td class="px-4 py-2 font-semibold text-right">{{ $fmt($secKey === 'equity' ? ((-1 * (float)($section['total'] ?? 0)) + (float)($plMainNet ?? 0)) : ($section['total'] ?? 0)) }}</td>
                                @foreach($report->columns as $col)
                                    @php
                                        $sum = (float) ($sectionComparativeTotals[$col->id][$secKey] ?? 0);
                                        if ($secKey === 'equity') {
                                            $sum = (-1 * $sum) + (float) ($sectionComparativeTotals[$col->id]['pl_net'] ?? 0);
                                        }
                                    @endphp
                                    <td class="px-4 py-2 font-semibold text-right">{{ $fmt($sum) }}</td>
                                @endforeach
                            </tr>

                            @if($layout === 'assets-minus-liabilities-equals-equity' && $secKey === 'liabilities')
                                <tr>
                                    @foreach($report->columns as $col)
                                        <td class="px-4 py-2 font-semibold text-right">{{ $fmt($netAssetsComparatives[$col->id] ?? 0) }}</td>
                                    @endforeach
                                </tr>
                            @endif

                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Accounting equation reconciliation -->
            @php
                $nonCurrentAssetsTotal = 0;
                foreach($sections ?? [] as $skey => $sval) {
                    $lk = strtolower($skey);
                    if (strpos($lk, 'non') !== false && strpos($lk, 'asset') !== false) {
                        $nonCurrentAssetsTotal += (float) ($sval['total'] ?? 0);
                    }
                }
            @endphp

            <div class="mt-6 mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Accounting Equation</h3>
                <div class="text-sm text-gray-700">
                    <div class="flex justify-between mb-1">
                        <div>Assets</div>
                         <div class="font-mono">{{ $fmt($sections['assets']['total'] ?? 0) }}</div>
                    </div>

                    @if($nonCurrentAssetsTotal > 0)
                        <div class="flex justify-between mb-1">
                            <div class="text-sm text-gray-600">Noncurrent Assets</div>
                            <div class="font-mono">{{ $fmt($nonCurrentAssetsTotal) }}</div>
                        </div>
                    @endif

                    <div class="flex justify-between mb-1">
                        <div>
                            {{ ($selectedEquation ?? 'standard') === 'extended'
                                ? 'Liabilities + Equity + Revenue - Expenses'
                                : 'Liabilities + Equity' }}
                        </div>
                        <div class="font-mono">{{ $fmt($equationRhsMain ?? 0) }}</div>
                    </div>

                    <div class="flex justify-between mb-1">
                        <div class="text-sm text-gray-600">Difference (Assets − Selected RHS)</div>
                        <div class="font-mono text-sm text-gray-800">{{ $fmt($equationDiffMain ?? 0) }}</div>
                    </div>

                    <div class="h-px bg-gray-200 my-3"></div>

                    <div class="flex justify-between mb-1">
                        <div>Standard RHS (Liabilities + Equity)</div>
                        <div class="font-mono">{{ $fmt($standardRhsMain ?? 0) }}</div>
                    </div>

                    <div class="flex justify-between">
                        <div>Extended RHS (Liabilities + Equity + Revenue - Expenses)</div>
                        <div class="font-mono">{{ $fmt($extendedRhsMain ?? 0) }}</div>
                    </div>
                    
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-gray-200 my-6"></div>

            <!-- Footer -->
            <div class="text-sm text-gray-700 whitespace-pre-line">
                {{ $report->footer }}
            </div>
        </div>
    </div>
</x-app-layout>
