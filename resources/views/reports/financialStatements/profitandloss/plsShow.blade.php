<x-app-layout>
<div class="min-h-screen p-4 md:p-6 lg:p-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">

        <!-- ACTION BAR -->
        <div class="mb-6 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <!-- Back Button -->
                <a href="{{ route('reports.financialStatements.profit-and-loss.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg 
                          text-gray-900 hover:bg-gray-100 transition shadow-sm">
                    <i class="fas fa-arrow-left text-gray-500"></i>
                    <span>Back to Reports</span>
                </a>

                <div class="flex flex-wrap gap-3">
                    
                    <!-- Edit button -->
                    <a href="{{ route('reports.financial.profit-and-loss.edit', $report) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg 
                              hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </a>

                    <!-- Export -->
                    <a href="{{ route('reports.financial.profit-and-loss.export', $report) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg 
                              hover:bg-emerald-700 transition shadow-sm">
                        <i class="fas fa-download"></i>
                        <span>Export CSV</span>
                    </a>

                    <!-- Print -->
                    <button onclick="window.print()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 text-white rounded-lg 
                                   hover:bg-gray-800 transition shadow-sm">
                        <i class="fas fa-print"></i>
                        <span>Print</span>
                    </button>

                </div>
            </div>
        </div>


        <!-- MAIN REPORT CARD -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <!-- HEADER -->
            <div class="p-8 text-center border-b border-gray-200 bg-white">
                <div class="text-gray-600 text-sm mb-2">
                    {{ $businessName }}
                </div>

                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $report->title }}
                </h1>

                <p class="mt-3 text-gray-800 font-medium">
                    {{ $report->date_from->format('d M Y') }} — {{ $report->date_to->format('d M Y') }}
                </p>

                <div class="mt-4 inline-flex items-center gap-2 px-4 py-1.5 bg-blue-100 text-blue-700 
                            rounded-full text-sm font-semibold capitalize">
                    <i class="fas fa-calculator"></i>
                    {{ $report->accounting_method }} basis
                </div>

                @if($report->description)
                    <p class="mt-4 text-gray-600 max-w-2xl mx-auto text-sm">
                        {{ $report->description }}
                    </p>
                @endif
            </div>


            <!-- BODY -->
            <div class="p-6 md:p-8">

                @if($grouped && count($grouped))

                    <!-- REVENUE SECTION -->
                    <section class="mb-12">
                        <h2 class="flex items-center gap-3 text-2xl font-bold text-green-700 mb-4">
                            <i class="fas fa-arrow-up"></i>
                            Revenue
                        </h2>

                        @foreach($revenueGroups as $groupName => $groupData)
                        <div class="mb-6">
                            <!-- Group Header -->
                            <div class="px-4 py-2 bg-green-50 border-l-4 border-green-600 rounded-md mb-2 font-semibold text-gray-900">
                                {{ $groupName }}
                            </div>

                            <!-- Accounts -->
                            <div class="divide-y divide-gray-100">
                                @foreach(($groupData['accounts'] ?? []) as $acc)
                                <div class="flex items-center justify-between py-2">
                                    <div class="text-gray-800">
                                        @if(($report->show_account_codes ?? false) && $acc['code'])
                                            <span class="text-gray-400 mr-2">{{ $acc['code'] }}</span>
                                        @endif
                                        {{ $acc['name'] }}
                                    </div>
                                    <div class="font-medium {{ ($acc['amount'] >= 0) ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $fmt($acc['amount'], true) }}
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Group Total -->
                            <div class="flex items-center justify-between mt-3 border-t pt-3 border-gray-200">
                                <span class="font-semibold text-gray-900">Total {{ $groupName }}</span>
                                <span class="font-bold text-green-700">
                                    {{ $fmt($groupData['total'], true) }}
                                </span>
                            </div>
                        </div>
                        @endforeach

                        <!-- Grand Total Revenue -->
                        <div class="flex items-center justify-between px-4 py-3 rounded-lg bg-green-50 border border-green-600 font-bold text-lg text-green-800 mt-6">
                            <span>Total Revenue</span>
                            <span>{{ $fmt($revenueTotal, true) }}</span>
                        </div>
                    </section>


                    <!-- EXPENSE SECTION -->
                    <section class="mb-12">
                        <h2 class="flex items-center gap-3 text-2xl font-bold text-red-700 mb-4">
                            <i class="fas fa-arrow-down"></i>
                            Expenses
                        </h2>

                        @foreach($expenseGroups as $groupName => $groupData)
                        <div class="mb-6">

                            <div class="px-4 py-2 bg-red-50 border-l-4 border-red-600 rounded-md mb-2 font-semibold text-gray-900">
                                {{ $groupName }}
                            </div>

                            <!-- Accounts -->
                            <div class="divide-y divide-gray-100">
                                @foreach(($groupData['accounts'] ?? []) as $acc)
                                <div class="flex items-center justify-between py-2">
                                    <div class="text-gray-800">{{ $acc['name'] }}</div>
                                    <div class="font-medium text-red-600">
                                        {{ $fmt(abs($acc['amount']), true) }}
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Group Total -->
                            <div class="flex items-center justify-between mt-3 border-t pt-3 border-gray-200">
                                <span class="font-semibold text-gray-900">Total {{ $groupName }}</span>
                                <span class="font-bold text-red-700">
                                    {{ $fmt(abs($groupData['total']), true) }}
                                </span>
                            </div>

                        </div>
                        @endforeach

                        <!-- Grand Total Expense -->
                        <div class="flex items-center justify-between px-4 py-3 rounded-lg bg-red-50 border border-red-600 font-bold text-lg text-red-800 mt-6">
                            <span>Total Expenses</span>
                            <span>{{ $fmt(abs($expenseTotal), true) }}</span>
                        </div>
                    </section>


                    <!-- NET PROFIT / LOSS -->
                    <section class="text-center py-10">
                        <div class="inline-flex items-center gap-3 px-6 py-3 rounded-lg
                            {{ $isProfit ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas {{ $isProfit ? 'fa-check-circle' : 'fa-exclamation-circle' }} text-xl"></i>
                            <span class="font-bold text-xl">
                                Net {{ $isProfit ? 'Profit' : 'Loss' }}: {{ $fmt($netProfit, true) }}
                            </span>
                        </div>
                    </section>

                @else

                    <!-- EMPTY STATE -->
                    <div class="py-16 text-center">
                        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-file text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">No Data Available</h3>
                        <p class="text-gray-600 mt-2">This report contains no financial records.</p>

                        <a href="{{ route('reports.financial.profit-and-loss.edit', $report) }}"
                           class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-blue-600 text-white rounded-lg 
                                  hover:bg-blue-700 transition shadow-sm">
                            <i class="fas fa-edit"></i>
                            Edit Report
                        </a>
                    </div>

                @endif
            </div>


            <!-- FOOTER -->
            <div class="px-8 py-4 bg-gray-100 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:justify-between text-sm text-gray-600">

                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i>
                        Generated on {{ now()->format('d M Y H:i') }}
                    </div>

                    <div class="flex items-center gap-2">
                        <i class="fas fa-user"></i>
                        Created by {{ $report->user->name ?? 'Unknown' }}
                    </div>

                </div>

                @if($report->footer)
                <div class="mt-4 border-t pt-4 text-gray-700 text-sm">
                    {!! nl2br(e($report->footer)) !!}
                </div>
                @endif
            </div>

        </div>


        <!-- METADATA -->
        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6 no-print">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>
                Report Information
            </h3>

            <div class="grid md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-gray-500">Report ID</p>
                    <p class="font-mono text-gray-900 text-base">#{{ $report->id }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Accounting Method</p>
                    <p class="text-gray-900 text-base capitalize">{{ $report->accounting_method }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Created At</p>
                    <p class="text-gray-900 text-base">{{ $report->created_at->format('d M Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Last Updated</p>
                    <p class="text-gray-900 text-base">{{ $report->updated_at->format('d M Y H:i') }}</p>
                </div>

            </div>
        </div>

    </div>
</div>
</x-app-layout>
