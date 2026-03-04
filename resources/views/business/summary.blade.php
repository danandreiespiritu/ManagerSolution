<x-app-layout>
    <div class="py-10 bg-gray-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800">Business Summary</h1>
                    <p class="text-gray-500 text-base mt-1">{{ $business->business_name ?? '' }}</p>
                </div>
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 bg-gray-700 hover:bg-gray-800 text-white rounded-md transition shadow-sm">
                    ← Back
                </a>
            </div>

            <!-- Top summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                @php
                    $summaryCards = [
                        ['label' => 'Total Assets', 'value' => $totals['assets'] ?? '-'],
                        ['label' => 'Total Liabilities', 'value' => $totals['liabilities'] ?? '-'],
                        ['label' => 'Equity', 'value' => $totals['equity'] ?? '-'],
                    ];
                @endphp

                @foreach ($summaryCards as $card)
                    <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 hover:shadow-md transition">
                        <div class="text-sm text-gray-500 font-medium uppercase tracking-wide">
                            {{ $card['label'] }}
                        </div>
                        <div class="mt-3 text-3xl font-bold text-gray-800">
                            {{ $card['value'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Secondary summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                @php
                    $additionalCards = [
                        ['label' => 'Income', 'value' => $totals['income'] ?? '-'],
                        ['label' => 'Expenses', 'value' => $totals['expenses'] ?? '-'],
                        ['label' => 'Net Profit (Loss)', 'value' => $totals['net'] ?? '-'],
                    ];
                @endphp

                @foreach ($additionalCards as $card)
                    <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 hover:shadow-md transition">
                        <div class="text-sm text-gray-500 font-medium uppercase tracking-wide">
                            {{ $card['label'] }}
                        </div>
                        <div class="mt-3 text-3xl font-bold text-gray-800">
                            {{ $card['value'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Financial Statements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                <!-- Balance Sheet -->
                <div>
                    <h2 class="text-center text-xl font-semibold text-gray-700 mb-6">
                        Balance Sheet
                    </h2>

                    @php
                        $balanceOrder = ['Current Assets', 'Current Liabilities', 'Equity'];
                        $orderedBalanceSections = collect($sections['balance_sheet'])
                            ->sortBy(fn($s) => array_search($s['name'], $balanceOrder))
                            ->values();
                    @endphp

                    <div class="space-y-6">
                        @foreach ($orderedBalanceSections as $group)
                            @php
                                // Copy original accounts
                                $accounts = collect($group['accounts']);

                                // Adjust Equity accounts
                                if (strtolower($group['name']) === 'equity') {
                                    $ownerCapital = $accounts->firstWhere('name', 'Owner’s Capital');
                                    $drawings = $accounts->firstWhere('name', 'Drawings / Owner Withdrawals');
                                    $retained = $accounts->firstWhere('name', 'Retained Earnings');

                                    $netProfit = floatval(str_replace(',', '', $totals['net'] ?? 0));

                                    if ($ownerCapital) {
                                        $ownerCapital['balance'] = number_format(abs(floatval(str_replace(',', '', $ownerCapital['balance']))), 2);
                                    }
                                    if ($drawings) {
                                        $drawings['balance'] = number_format(-1 * floatval(str_replace(',', '', $drawings['balance'])), 2);
                                    }
                                    if ($retained) {
                                        $retained['balance'] = number_format($netProfit, 2);
                                    }

                                    // Rebuild accounts in desired order
                                    $accounts = collect([$ownerCapital, $drawings, $retained])->filter();
                                }
                            @endphp

                            <div class="rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden">
                                <!-- Group Header -->
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-800 font-medium">{{ $group['name'] }}</span>
                                        <span class="text-gray-600 font-semibold">
                                            {{ number_format($accounts->sum(fn($a) => floatval(str_replace(',', '', $a['balance']))), 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Accounts List -->
                                <ul class="divide-y divide-gray-100 px-6 py-4">
                                    @forelse ($accounts as $acct)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $acct['name'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $acct['balance'] }}</span>
                                        </li>
                                    @empty
                                        <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Profit & Loss -->
                <div>
                    <h2 class="text-center text-xl font-semibold text-gray-700 mb-6">
                        Profit and Loss Statement
                    </h2>

                    @php
                        // Define the order: Income first, Operating Expenses second
                        $plOrder = ['Income', 'Operating Expenses'];
                        $orderedPLSections = collect($sections['profit_and_loss'])
                            ->sortBy(fn($s) => array_search($s['name'], $plOrder) !== false ? array_search($s['name'], $plOrder) : 999)
                            ->values();
                    @endphp

                    <div class="space-y-6">
                        @foreach ($orderedPLSections as $group)
                            <div class="rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden">

                                <!-- Group Header -->
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-800 font-medium">{{ $group['name'] }}</span>
                                        <span class="text-gray-600 font-semibold">
                                            @php
                                                // Compute total dynamically
                                                $displayTotal = $group['name'] === 'Income'
                                                    ? (float) str_replace(',', '', $totals['income'] ?? 0)
                                                    : collect($group['accounts'])->sum(fn($acct) =>
                                                        (float) str_replace(',', '', $acct['balance'] ?? 0)
                                                    );
                                            @endphp

                                            {{ number_format($displayTotal, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Accounts List -->
                                <ul class="divide-y divide-gray-100 px-6 py-4">
                                    @forelse ($group['accounts'] as $acct)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $acct['name'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $acct['balance'] }}</span>
                                        </li>
                                    @empty
                                        <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
