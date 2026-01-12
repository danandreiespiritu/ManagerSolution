<x-app-layout>
    @php
        $fmt = function($v, $showSign = false) {
            if ($v === null) return '—';
            $v = (float)$v;
            if (abs($v) < 0.005) return '—';
            if ($v < 0) return '(' . number_format(abs($v), 2) . ')';
            $formatted = number_format($v, 2);
            return $showSign && $v > 0 ? '+' . $formatted : $formatted;
        };
    @endphp

    <div class="max-w-6xl mx-auto px-4 py-6">
        <!-- Back button -->
        <div class="mb-4">
            <a href="{{ route('reports.financial.changes-in-equity.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase shadow-sm hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="text-gray-700 font-semibold">{{ $business->name ?? '' }}</div>
            <div class="text-2xl md:text-3xl font-semibold text-gray-900 mt-1">{{ $title ?? 'Statement of Changes in Equity' }}</div>
            <div class="text-gray-600 mt-2">
                For the period from {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}
                to {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
            </div>

            <div class="text-sm text-gray-500 mt-1">
                <span class="px-2 py-1 bg-{{ $basis === 'cash' ? 'blue' : 'purple' }}-100 text-{{ $basis === 'cash' ? 'blue' : 'purple' }}-700 rounded">
                    {{ ucfirst($basis ?? 'accrual') }} Basis
                </span>
            </div>

            @if(!empty($description))
                <div class="text-gray-500 text-sm mt-1">{{ $description }}</div>
            @endif
        </div>

        <!-- Report -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">

            @if(($basis ?? 'accrual') === 'cash' && isset($beginningEquity))
                <!-- =========================
                     CASH BASIS LAYOUT
                ========================== -->
                <div class="p-6 space-y-4">

                    <div class="grid grid-cols-2 text-sm">
                        <div class="font-semibold text-gray-900">Owner's Capital, Beginning</div>
                        <div class="text-right font-semibold text-gray-900">
                            {{ $fmt($beginningEquity) }}
                        </div>
                    </div>

                    <div class="ml-8 space-y-2">
                        <div class="grid grid-cols-2 text-sm">
                            <div class="text-gray-700">Add: Cash Investments</div>
                            <div class="text-right text-green-700">{{ $fmt($investments ?? 0) }}</div>
                        </div>

                        <div class="grid grid-cols-2 text-sm">
                            <div class="text-gray-700">
                                Add: Net {{ ($netIncome ?? 0) >= 0 ? 'Income' : 'Loss' }} (Cash Basis)
                            </div>
                            <div class="text-right {{ ($netIncome ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ $fmt($netIncome ?? 0) }}
                            </div>
                        </div>

                        @php $subtotal = ($beginningEquity ?? 0) + ($investments ?? 0) + ($netIncome ?? 0); @endphp
                        <div class="grid grid-cols-2 text-sm pt-2 border-t">
                            <div class="font-medium text-gray-800 ml-4">Subtotal</div>
                            <div class="text-right font-medium text-gray-800">{{ $fmt($subtotal) }}</div>
                        </div>

                        <div class="grid grid-cols-2 text-sm">
                            <div class="text-gray-700">Less: Cash Withdrawals</div>
                            <div class="text-right text-red-700">{{ $fmt($withdrawals ?? 0) }}</div>
                        </div>
                    </div>

                    @php
                        $netChange = ($netIncome ?? 0) + ($investments ?? 0) - ($withdrawals ?? 0);
                    @endphp

                    <div class="grid grid-cols-2 text-sm pt-3 border-t">
                        <div class="font-semibold">
                            Net {{ $netChange >= 0 ? 'Increase' : 'Decrease' }} in Owner's Equity
                        </div>
                        <div class="text-right font-semibold {{ $netChange >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ $fmt($netChange, true) }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 text-base pt-4 border-t-2">
                        <div class="font-bold text-gray-900">Owner's Capital, Ending</div>
                        <div class="text-right font-bold text-gray-900">{{ $fmt($totalEquity ?? 0) }}</div>
                    </div>

                    @if(isset($verifiedEndingEquity) && abs(($totalEquity ?? 0) - $verifiedEndingEquity) > 0.01)
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm">
                            <div class="font-medium text-yellow-800">⚠ Reconciliation Notice</div>
                            <div class="text-yellow-700 mt-1">
                                Calculated: {{ $fmt($totalEquity ?? 0) }} |
                                Verified: {{ $fmt($verifiedEndingEquity) }} |
                                Difference: {{ $fmt(abs(($totalEquity ?? 0) - $verifiedEndingEquity)) }}
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                        <strong>Note:</strong> Cash basis excludes non-cash transactions.
                    </div>

                </div>

            @else
                <!-- =========================
                     ACCRUAL BASIS FORMAT
                ========================== -->
                @include('reports.financial.changes-in-equity.accrual-layout')
            @endif

            <!-- Footer -->
            @if($report->footer ?? null)
                <div class="p-4 border-t text-sm text-gray-600">
                    {!! nl2br(e($report->footer)) !!}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
