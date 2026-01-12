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
                    As at {{ \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}
                </div>

                <div class="mt-1 inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold tracking-wide">
                    {{ ucfirst($report->accounting_method) }} Basis
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-gray-200 my-4"></div>

            <!-- Right aligned date -->
            <div class="text-right text-sm text-gray-600">
                {{ \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}
            </div>

            @php
                // Column headers: main + comparatives
                $columns = collect([['id' => 'main', 'label' => \Carbon\Carbon::parse($report->date)->format('d/m/Y')]])
                    ->merge($report->columns->map(fn($c) => ['id' => $c->id, 'label' => \Carbon\Carbon::parse($c->date)->format('d/m/Y')]));

                // Formatting
                $fmt = function($v){
                    $v = (float)$v;
                    if (abs($v) < 0.005) return '-';
                    return $v < 0 ? '(' . number_format(abs($v), 2) . ')' : number_format($v, 2);
                };
            @endphp

            <!-- Sections -->
            <div class="mt-6 space-y-10">

                @php
                    $renderOrder = $order ?? ['assets', 'liabilities', 'equity'];
                @endphp

                @foreach($renderOrder as $secKey)
                    @php $section = $sections[$secKey] ?? null; @endphp

                    @if($section)
                        <!-- Section Title -->
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">
                            {{ $section['title'] }}
                        </h2>

                        <!-- Groups -->
                        @foreach(($section['groups'] ?? collect()) as $groupName => $accounts)
                            <div class="mb-3">
                                <div class="text-sm font-medium text-gray-700 mb-1 pl-1">
                                    {{ $groupName }}
                                </div>

                                <!-- Accounts -->
                                @foreach($accounts as $acc)
                                    <div class="grid grid-cols-12 gap-2 py-1 text-sm">
                                        <div class="col-span-7 pl-4 text-gray-900">
                                            {{ $acc->account_name ?? '' }}
                                        </div>

                                        <div class="col-span-5 grid grid-cols-{{ max(1, $columns->count()) }} gap-2">
                                            <div class="text-right text-gray-900">
                                                {{ $fmt($acc->balance) }}
                                            </div>

                                            @foreach($report->columns as $col)
                                                <div class="text-right text-gray-900">
                                                    {{ $fmt((float)($acc->comparatives[$col->id] ?? 0)) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <!-- TOTAL -->
                        <div class="grid grid-cols-12 gap-2 py-2 border-t border-gray-300 font-semibold text-gray-900 mt-3">
                            <div class="col-span-7 pl-4">
                                Total — {{ $section['title'] }}
                            </div>

                            <div class="col-span-5 grid grid-cols-{{ max(1, $columns->count()) }} gap-2">
                                <div class="text-right">
                                    {{ $fmt($section['total'] ?? 0) }}
                                </div>

                                @foreach($report->columns as $col)
                                    @php $sum = (float) ($sectionComparativeTotals[$col->id][$secKey] ?? 0); @endphp
                                    <div class="text-right">
                                        {{ $fmt($sum) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Special Layout Rules -->
                        @if($layout === 'assets-minus-liabilities-equals-equity' && $secKey === 'liabilities')
                            <div class="grid grid-cols-12 gap-2 py-2 mt-2 font-semibold text-gray-900">
                                <div class="col-span-7 pl-4">Net Assets</div>
                                <div class="col-span-5 grid grid-cols-{{ max(1, $columns->count()) }} gap-2">
                                    <div class="text-right">{{ $fmt($netAssets ?? 0) }}</div>
                                    @foreach($report->columns as $col)
                                        <div class="text-right">{{ $fmt($netAssetsComparatives[$col->id] ?? 0) }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    @endif
                @endforeach
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
