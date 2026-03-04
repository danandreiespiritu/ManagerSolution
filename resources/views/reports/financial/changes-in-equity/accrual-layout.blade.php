<div class="w-full overflow-x-auto px-4 py-3 text-sm text-gray-600">
    <div class="grid grid-cols-12 gap-2 font-medium mb-2">
        <div class="col-span-7"></div>
    </div>
    <div class="h-px bg-gray-200"></div>

    <div class="divide-y">
        @php
            $totalOpening = 0; 
            $totalChanges = 0; 
            $totalClosing = 0;

            // Ensure Owner's Capital appears first
            $orderedSections = [];

            foreach ($sections as $group => $sec) {
                $low = strtolower($group);

                if (str_contains($low, 'capital')) {
                    $orderedSections = [$group => $sec] + $orderedSections;
                } else {
                    $orderedSections[$group] = $sec;
                }
            }
        @endphp

        @foreach($orderedSections as $group => $sec)
            @php
                $groupOpeningRaw = $sec['opening'] ?? 0;
                $groupEndingRaw = $sec['ending'] ?? 0;
                $groupMovementRaw = $sec['movement'] ?? ($groupEndingRaw - $groupOpeningRaw);

                // Equity is credit-normal
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
                    @php
                        $accountsList = $sec['accounts'] ?? [];
                        if (strtolower($group) === 'equity') {
                            $ownerCapital = null; $drawings = null; $retained = null;
                            foreach ($accountsList as $a) {
                                $name = strtolower($a['name'] ?? '');
                                if (str_contains($name, 'owner') && str_contains($name, 'capital')) { $ownerCapital = $a; continue; }
                                if (str_contains($name, 'draw') || str_contains($name, 'withdraw')) { $drawings = $a; continue; }
                                if (str_contains($name, 'retained')) { $retained = $a; continue; }
                            }
                            $newOrder = [];
                            if ($ownerCapital) $newOrder[] = $ownerCapital;
                            if ($drawings) $newOrder[] = $drawings;
                            if ($retained) $newOrder[] = $retained;
                            foreach ($accountsList as $a) {
                                $found = false;
                                foreach ($newOrder as $n) { if (($n['id'] ?? null) === ($a['id'] ?? null)) { $found = true; break; } }
                                if (! $found) $newOrder[] = $a;
                            }
                            $accountsList = $newOrder;
                        }
                    @endphp

                    @foreach($accountsList as $acc)
                        @php
                            $id = $acc['id'] ?? null;
                            $openRaw = $openingMap[$id] ?? 0;
                            $endRaw = $endingMap[$id] ?? 0;

                            $opening = -1 * $openRaw;
                            $closing = -1 * $endRaw;
                            $changes = $closing - $opening;

                            $changeClass = $changes > 0 
                                ? 'text-green-700' 
                                : ($changes < 0 ? 'text-red-700' : 'text-gray-700');
                        @endphp

                        <div class="grid grid-cols-12 gap-2 text-sm items-center">
                            <div class="col-span-7 truncate pl-8">
                                {{ $acc['name'] ?? '' }}
                            </div>
                            <div class="col-span-2 text-right">
                                {{ $fmt($opening) }}
                            </div>
                            <div class="col-span-2 text-right {{ $changeClass }}">
                                {{ $fmt($changes, true) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Remove bold total for first section (Owner's Capital) --}}
                @if(!$loop->first)
                <div class="mt-3 pt-2 border-t grid grid-cols-12 gap-2 text-sm font-semibold text-gray-800">
                    <div class="col-span-7">Total — {{ $group }}</div>
                    <div class="col-span-2 text-right">
                        {{ $fmt($groupOpening) }}
                    </div>
                    <div class="col-span-2 text-right {{ $groupChanges > 0 ? 'text-green-700' : ($groupChanges < 0 ? 'text-red-700' : 'text-gray-800') }}">
                        {{ $fmt($groupChanges, true) }}
                    </div>
                    <div class="col-span-1 text-right">
                        {{ $fmt($groupClosing) }}
                    </div>
                </div>
                @endif
            </div>
        @endforeach


        {{-- FIRST TOTAL EQUITY (before profit/loss) --}}
        <div class="px-4 pb-2">
            <div class="h-px bg-gray-200"></div>
            @php 
                $baseChangeClass = $totalChanges > 0 
                    ? 'text-green-700' 
                    : ($totalChanges < 0 ? 'text-red-700' : 'text-gray-900'); 
            @endphp
            <div class="mt-2 grid grid-cols-12 gap-2 text-sm font-bold text-gray-900">
                <div class="col-span-7">Total Equity</div>
                <div class="col-span-2 text-right">
                    {{ $fmt($totalOpening) }}
                </div>
                <div class="col-span-2 text-right {{ $baseChangeClass }}">
                    {{ $fmt($totalChanges, true) }}
                </div>
                <div class="col-span-1 text-right">
                </div>
            </div>
        </div>


        {{-- NET PROFIT / LOSS --}}
        @if(isset($profitLoss) && abs($profitLoss) > 0.01)
            @php 
                $plClass = $profitLoss > 0 
                    ? 'text-green-700' 
                    : 'text-red-700'; 
            @endphp
            <div class="px-4 pb-2">
                <div class="grid grid-cols-12 gap-2 text-sm font-medium text-gray-700 bg-blue-50 p-2 rounded">
                    <div class="col-span-7">
                        Net {{ $profitLoss >= 0 ? 'profit' : 'loss' }} for the period
                    </div>
                    <div class="col-span-2 text-right">—</div>
                    <div class="col-span-2 text-right {{ $plClass }}">
                        {{ $fmt($profitLoss, true) }}
                    </div>
                    <div class="col-span-1 text-right">
                    </div>
                </div>
            </div>
        @endif


        {{-- FINAL TOTAL EQUITY (BASE + PROFIT/LOSS) --}}
        @php
            $finalOpening = $totalOpening;
            $finalChanges = $totalChanges + ($profitLoss ?? 0);
            $finalClosing = $totalClosing + ($profitLoss ?? 0);

            $finalChangeClass = $finalChanges > 0 
                ? 'text-green-700' 
                : ($finalChanges < 0 ? 'text-red-700' : 'text-gray-900');
        @endphp

        <div class="px-4 pb-4">
            <div class="h-px bg-gray-200"></div>
            <div class="mt-2 grid grid-cols-12 gap-2 font-bold text-gray-900">
                <div class="col-span-7">Total Equity</div>
                <div class="col-span-2 text-right">
                    {{ $fmt($finalOpening) }}
                </div>
                <div class="col-span-2 text-right {{ $finalChangeClass }}">
                    {{ $fmt($finalChanges, true) }}
                </div>
            </div>
        </div>

    </div>
</div>