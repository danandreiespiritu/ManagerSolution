<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Business Summary</h1>
                    <p class="text-gray-600 mt-1">{{ $business->business_name ?? '' }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">← Back</a>
            </div>

            <!-- Top summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                <div class="rounded-xl border-2 border-cyan-200 bg-white p-6 shadow-lg hover:shadow-xl transition">
                    <div class="text-sm text-cyan-700 font-bold uppercase tracking-wide">Total Assets</div>
                    <div class="mt-3 text-4xl font-black text-gray-900">{{ $totals['assets'] ?? '-' }}</div>
                </div>
                <div class="rounded-xl border-2 border-red-200 bg-white p-6 shadow-lg hover:shadow-xl transition">
                    <div class="text-sm text-red-700 font-bold uppercase tracking-wide">Total Liabilities</div>
                    <div class="mt-3 text-4xl font-black text-gray-900">{{ $totals['liabilities'] ?? '-' }}</div>
                </div>
                <div class="rounded-xl border-2 border-indigo-200 bg-white p-6 shadow-lg hover:shadow-xl transition">
                    <div class="text-sm text-indigo-700 font-bold uppercase tracking-wide">Equity</div>
                    <div class="mt-3 text-4xl font-black text-gray-900">{{ $totals['equity'] ?? '-' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                <div class="rounded-xl border-2 border-yellow-200 bg-white p-6 shadow-lg hover:shadow-xl transition">
                    <div class="text-sm text-yellow-800 font-bold uppercase tracking-wide">Income</div>
                    <div class="mt-3 text-4xl font-black text-gray-900">{{ $totals['income'] ?? '-' }}</div>
                </div>
                <div class="rounded-xl border-2 border-blue-200 bg-white p-6 shadow-lg hover:shadow-xl transition">
                    <div class="text-sm text-blue-700 font-bold uppercase tracking-wide">Expenses</div>
                    <div class="mt-3 text-4xl font-black text-gray-900">{{ $totals['expenses'] ?? '-' }}</div>
                </div>
                <div class="rounded-xl border-2 border-teal-200 bg-white p-6 shadow-lg hover:shadow-xl transition">
                    <div class="text-sm text-teal-700 font-bold uppercase tracking-wide">Net Profit (Loss)</div>
                    <div class="mt-3 text-4xl font-black text-gray-900">{{ $totals['net'] ?? '-' }}</div>
                </div>
            </div>

            <!-- Financial Statements -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <!-- Balance Sheet Column -->
                <div>
                    <div class="text-center mb-6">
                        <h2 class="text-lg font-bold text-gray-700 uppercase tracking-wider">Balance Sheet</h2>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-xl bg-white border-2 border-cyan-200 overflow-hidden shadow-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b-2 border-cyan-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-900 text-base">Assets</span>
                                    <span class="font-semibold text-cyan-700 text-lg">{{ $totals['assets'] ?? '-' }}</span>
                                </div>
                            </div>
                            <ul class="divide-y divide-gray-100 px-6 py-4 text-sm">
                                @if(!empty($balanceSheet['Assets'] ?? []))
                                    @foreach($balanceSheet['Assets'] as $line)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $line['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $line['amount'] }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                @endif
                            </ul>
                        </div>

                        <div class="rounded-xl bg-white border-2 border-red-200 overflow-hidden shadow-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b-2 border-red-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-900 text-base">Liabilities</span>
                                    <span class="font-semibold text-red-700 text-lg">{{ $totals['liabilities'] ?? '-' }}</span>
                                </div>
                            </div>
                            <ul class="divide-y divide-gray-100 px-6 py-4 text-sm">
                                @if(!empty($balanceSheet['Liabilities'] ?? []))
                                    @foreach($balanceSheet['Liabilities'] as $line)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $line['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $line['amount'] }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                @endif
                            </ul>
                        </div>

                        <div class="rounded-xl bg-white border-2 border-indigo-200 overflow-hidden shadow-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b-2 border-indigo-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-900 text-base">Equity</span>
                                    <span class="font-semibold text-indigo-700 text-lg">{{ $totals['equity'] ?? '-' }}</span>
                                </div>
                            </div>
                            <ul class="divide-y divide-gray-100 px-6 py-4 text-sm">
                                @if(!empty($balanceSheet['Equity'] ?? []))
                                    @foreach($balanceSheet['Equity'] as $line)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $line['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $line['amount'] }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Profit & Loss Column -->
                <div>
                    <div class="text-center mb-6">
                        <h2 class="text-lg font-bold text-gray-700 uppercase tracking-wider">Profit and Loss Statement</h2>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-xl bg-white border-2 border-yellow-200 overflow-hidden shadow-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b-2 border-yellow-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-900 text-base">Income</span>
                                    <span class="font-semibold text-yellow-800 text-lg">{{ $totals['income'] ?? '-' }}</span>
                                </div>
                            </div>
                            <ul class="divide-y divide-gray-100 px-6 py-4 text-sm">
                                @if(!empty($profitLoss['income'] ?? []))
                                    @foreach($profitLoss['income'] as $line)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $line['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $line['amount'] }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                @endif
                            </ul>
                        </div>

                        <div class="rounded-xl bg-white border-2 border-blue-200 overflow-hidden shadow-lg">
                            <div class="px-6 py-4 bg-gray-50 border-b-2 border-blue-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-900 text-base">Expenses</span>
                                    <span class="font-semibold text-blue-700 text-lg">{{ $totals['expenses'] ?? '-' }}</span>
                                </div>
                            </div>
                            <ul class="divide-y divide-gray-100 px-6 py-4 text-sm">
                                @if(!empty($profitLoss['expenses'] ?? []))
                                    @foreach($profitLoss['expenses'] as $line)
                                        <li class="py-3 flex justify-between text-gray-700">
                                            <span>{{ $line['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $line['amount'] }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="py-6 text-center text-gray-400 italic">No data available</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>