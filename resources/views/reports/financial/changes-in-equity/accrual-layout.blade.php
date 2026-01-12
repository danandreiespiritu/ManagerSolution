<div class="px-4 py-3 text-sm text-gray-600">
    <div class="grid grid-cols-12 gap-2 font-medium mb-2">
        <div class="col-span-7"></div>
        <div class="col-span-2 text-right">Opening Balance</div>
        <div class="col-span-2 text-right">Changes</div>
        <div class="col-span-1 text-right">Closing Balance</div>
    </div>
    <div class="h-px bg-gray-200"></div>

    <div class="divide-y">
        @php
            $totalOpening = 0; $totalChanges = 0; $totalClosing = 0;
        @endphp

        @foreach($sections as $group => $sec)
            @php
                $groupOpeningRaw = $sec['opening'] ?? 0;
                $groupEndingRaw = $sec['ending'] ?? 0;
                $groupMovementRaw = $sec['movement'] ?? ($groupEndingRaw - $groupOpeningRaw);

                // For equity (credit-normal) we display as the inverse of ledger balances
                $groupOpening = -1 * $groupOpeningRaw;
                $groupChanges = -1 * $groupMovementRaw;
                $groupClosing = -1 * $groupEndingRaw;

                $totalOpening += $groupOpening;
                $totalChanges += $groupChanges;
                $totalClosing += $groupClosing;
            @endphp

            <div class="p-4">
                <div class="text-gray-700 font-semibold mb-2">{{ $group }}</div>
                <div class="space-y-1">
                    @foreach($sec['accounts'] as $acc)
                        @php
                            $id = $acc['id'] ?? null;
                            $openRaw = $openingMap[$id] ?? 0;
                            $endRaw = $endingMap[$id] ?? 0;
                            $opening = -1 * $openRaw;
                            $closing = -1 * $endRaw;
                            $changes = $closing - $opening;
                            $changeClass = $changes > 0 ? 'text-green-700' : ($changes < 0 ? 'text-red-700' : 'text-gray-700');
                        @endphp

                        <div class="grid grid-cols-12 gap-2 text-sm items-center">
                            <div class="col-span-7 truncate pl-8">{{ $acc['name'] ?? '' }}</div>
                            <div class="col-span-2 text-right">{{ $fmt($opening) }}</div>
                            <div class="col-span-2 text-right {{ $changeClass }}">{{ $fmt($changes, true) }}</div>
                            <div class="col-span-1 text-right">{{ $fmt($closing) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 pt-2 border-t grid grid-cols-12 gap-2 text-sm font-semibold text-gray-800">
                    <div class="col-span-7">Total — {{ $group }}</div>
                    <div class="col-span-2 text-right">{{ $fmt($groupOpening) }}</div>
                    <div class="col-span-2 text-right {{ $groupChanges > 0 ? 'text-green-700' : ($groupChanges < 0 ? 'text-red-700' : 'text-gray-800') }}">{{ $fmt($groupChanges, true) }}</div>
                    <div class="col-span-1 text-right">{{ $fmt($groupClosing) }}</div>
                </div>
            </div>
        @endforeach

        @if(isset($profitLoss) && abs($profitLoss) > 0.01)
            @php $plClass = $profitLoss > 0 ? 'text-green-700' : 'text-red-700'; @endphp
            <div class="px-4 pb-2">
                <div class="grid grid-cols-12 gap-2 text-sm font-medium text-gray-700 bg-blue-50 p-2 rounded">
                    <div class="col-span-7">Net {{ $profitLoss >= 0 ? 'profit' : 'loss' }} for the period</div>
                    <div class="col-span-2 text-right">—</div>
                    <div class="col-span-2 text-right {{ $plClass }}">{{ $fmt($profitLoss, true) }}</div>
                    <div class="col-span-1 text-right">{{ $fmt($profitLoss) }}</div>
                </div>
            </div>
        @endif

        <div class="px-4 pb-4">
            <div class="h-px bg-gray-200"></div>
            @php $totalChangeClass = $totalChanges > 0 ? 'text-green-700' : ($totalChanges < 0 ? 'text-red-700' : 'text-gray-900'); @endphp
            <div class="mt-2 grid grid-cols-12 gap-2 text-sm font-bold text-gray-900">
                <div class="col-span-7">Total Equity</div>
                <div class="col-span-2 text-right">{{ $fmt($totalOpening) }}</div>
                <div class="col-span-2 text-right {{ $totalChangeClass }}">{{ $fmt($totalChanges, true) }}</div>
                <div class="col-span-1 text-right">{{ $fmt($totalClosing) }}</div>
            </div>
            <div class="mt-2 h-px bg-gray-200"></div>
        </div>
    </div>
</div>
