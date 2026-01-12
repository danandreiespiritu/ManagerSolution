<x-app-layout>
  <style>
    html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif; }
    .cfs-divider { border-bottom: 2px solid #e5e7eb; margin: 1.5rem 0; }
    .cfs-section-header { background: linear-gradient(to right, #f9fafb, #ffffff); border-left: 4px solid #3b82f6; padding: 0.75rem 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; }
    .cfs-line-item:hover { background-color: #f9fafb; }
    .cfs-total-row { border-top: 2px solid #d1d5db; border-bottom: 1px solid #d1d5db; background-color: #f9fafb; font-weight: 600; }
    .cfs-grand-total { border-top: 3px double #374151; border-bottom: 3px double #374151; background-color: #f3f4f6; font-weight: 700; }
    .negative-amount { color: #dc2626; } .positive-amount { color: #059669; }
    @media print { .no-print { display: none !important; } body { background: white; } .cfs-container { box-shadow: none; border: none; } }
    @media (max-width: 768px) { .cfs-grid { font-size: 0.875rem; } }
  </style>

  <div class="min-h-screen p-4 md:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto bg-white border border-gray-200 rounded-lg shadow-lg cfs-container">
      <div class="p-6 md:p-8 border-b border-gray-200 bg-gradient-to-br from-blue-50 to-white">
        <div class="text-center">
          <div class="text-sm md:text-base text-gray-600 font-medium mb-2">{{ $business->name ?? '' }}</div>
          <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Cash Flow Statement</h1>
          <div class="mt-2 text-base md:text-lg text-gray-700 font-medium">For the period {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
          <div class="mt-2 inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full capitalize"><i class="fas fa-chart-line mr-2"></i>{{ $method }} Method</div>
          @if(!empty($description))<div class="mt-4 text-sm md:text-base text-gray-600 max-w-3xl mx-auto">{{ $description }}</div>@endif
        </div>

        <div class="mt-6 flex flex-wrap justify-center gap-3 no-print">
          @if(!empty($reportId))
            <a href="{{ route('reports.financial.cashflow.exportSaved', $reportId) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm" aria-label="Export to CSV"><i class="fas fa-file-csv mr-2"></i>Export CSV</a>
            <a href="{{ route('reports.financial.cashflow.editSaved', $reportId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm" aria-label="Edit Report"><i class="fas fa-edit mr-2"></i>Edit Report</a>
          @else
            <a href="{{ route('reports.financial.cashflow.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm" aria-label="Export to CSV"><i class="fas fa-file-csv mr-2"></i>Export CSV</a>
          @endif
          <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors shadow-sm" aria-label="Print Report"><i class="fas fa-print mr-2"></i>Print</button>
          <a href="{{ route('reports.financial.cashflow.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 border border-gray-300 transition-colors shadow-sm" aria-label="Back to Reports"><i class="fas fa-arrow-left mr-2"></i>Back to Reports</a>
        </div>
      </div>

    @php
      $fmt = function($v){ $v = (float)$v; if (abs($v) < 0.005) return '-'; return ($v < 0 ? '(' . number_format(abs($v),2) . ')' : number_format($v,2)); };
      $columns = collect([['key' => 'main', 'label' => ($columnLabel ?: (\Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y'))), 'data' => $main]])->merge(collect($comparatives)->map(function($c){ $res = $c['result'] ?? []; $label = $c['label'] ?? null; return ['key' => 'comp', 'label' => $label ?: 'Comparative', 'data' => $res]; }));
    @endphp

      <div class="p-6 md:p-8">
        @php
          $fmt = function($v) { $v = (float)$v; if (abs($v) < 0.005) return '—'; $formatted = number_format(abs($v), 2); if ($v < 0) { return '<span class="negative-amount">(' . $formatted . ')</span>'; } return '<span class="positive-amount">' . $formatted . '</span>'; };
          $columns = collect([['key' => 'main','label' => ($columnLabel ?: (\Carbon\Carbon::parse($from)->format('d M Y') . ' – ' . \Carbon\Carbon::parse($to)->format('d M Y'))),'data' => $main]])->merge(collect($comparatives)->map(function($c) { $res = $c['result'] ?? $c['data'] ?? []; $label = $c['label'] ?? 'Comparative'; return ['key' => 'comp','label' => $label,'data' => $res]; }));
          $colCount = $columns->count(); $sectionOrder = ['operating', 'investing', 'financing'];
        @endphp

        <div class="mb-6 overflow-x-auto">
          <div class="grid gap-2 text-sm font-semibold text-gray-600" style="grid-template-columns: minmax(200px, 2fr) repeat({{ $colCount }}, minmax(120px, 1fr));">
            <div class="px-4 py-3 bg-gray-50 rounded-l-lg">Description</div>
            @foreach($columns as $col)
              <div class="px-4 py-3 bg-gray-50 text-right {{ $loop->last ? 'rounded-r-lg' : '' }}">{{ $col['label'] }}</div>
            @endforeach
          </div>
        </div>

        @foreach($sectionOrder as $secKey)
          @php
            $secTitle = match($secKey) { 'operating' => 'Operating Activities', 'investing' => 'Investing Activities', 'financing' => 'Financing Activities', default => ucfirst($secKey) . ' Activities' };
            $allNames = collect(); foreach ($columns as $c) { $lines = data_get($c['data'], 'sections.' . $secKey . '.lines', []); foreach ($lines as $ln) { $allNames->push($ln['name']); } } $allNames = $allNames->unique()->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();
          @endphp

          <div class="cfs-section-header rounded"><h2 class="text-lg font-bold text-gray-800"><i class="fas {{ $secKey === 'operating' ? 'fa-cog' : ($secKey === 'investing' ? 'fa-chart-line' : 'fa-coins') }} mr-2 text-blue-600"></i>{{ $secTitle }}</h2></div>

          @if($allNames->isEmpty())
            <div class="px-4 py-3 text-gray-500 italic text-sm">No transactions in this period</div>
          @else
            <div class="overflow-x-auto">
              @foreach($allNames as $name)
                <div class="cfs-line-item grid gap-2 py-2 px-2 transition-colors" style="grid-template-columns: minmax(200px, 2fr) repeat({{ $colCount }}, minmax(120px, 1fr));">
                  <div class="px-2 text-gray-700 text-sm">{{ $name }}</div>
                  @foreach($columns as $col)
                    @php $lines = collect(data_get($col['data'], 'sections.' . $secKey . '.lines', [])); $amount = optional($lines->firstWhere('name', $name))['amount'] ?? 0; @endphp
                    <div class="px-2 text-right text-sm font-medium">{!! $fmt($amount) !!}</div>
                  @endforeach
                </div>
              @endforeach
            </div>
          @endif

          <div class="cfs-total-row grid gap-2 py-3 px-2 mt-2" style="grid-template-columns: minmax(200px, 2fr) repeat({{ $colCount }}, minmax(120px, 1fr));">
            <div class="px-2 font-semibold text-gray-900">Net Cash from {{ $secTitle }}</div>
            @foreach($columns as $col) @php $sum = (float) data_get($col['data'], 'totals.' . $secKey, 0); @endphp <div class="px-2 text-right font-semibold text-gray-900">{!! $fmt($sum) !!}</div> @endforeach
          </div>
        @endforeach

        <div class="mt-8 space-y-1">
          <div class="cfs-grand-total grid gap-2 py-3 px-2 rounded" style="grid-template-columns: minmax(200px, 2fr) repeat({{ $colCount }}, minmax(120px, 1fr));"><div class="px-2 font-bold text-gray-900">Net Increase (Decrease) in Cash</div>@foreach($columns as $col)<div class="px-2 text-right font-bold text-gray-900">{!! $fmt((float) data_get($col['data'], 'netIncrease', 0)) !!}</div>@endforeach</div>

          <div class="grid gap-2 py-2 px-2 bg-gray-50" style="grid-template-columns: minmax(200px, 2fr) repeat({{ $colCount }}, minmax(120px, 1fr));"><div class="px-2 text-gray-700 font-medium">Cash and Cash Equivalents at Beginning of Period</div>@foreach($columns as $col)<div class="px-2 text-right text-gray-900 font-medium">{!! $fmt((float) data_get($col['data'], 'cashBeginning', 0)) !!}</div>@endforeach</div>

          <div class="cfs-grand-total grid gap-2 py-3 px-2 rounded" style="grid-template-columns: minmax(200px, 2fr) repeat({{ $colCount }}, minmax(120px, 1fr));"><div class="px-2 font-bold text-gray-900">Cash and Cash Equivalents at End of Period</div>@foreach($columns as $col)<div class="px-2 text-right font-bold text-gray-900">{!! $fmt((float) data_get($col['data'], 'cashEnding', 0)) !!}</div>@endforeach</div>
        </div>
      </div>

      @if(!empty($footer))<div class="p-6 md:p-8 border-t border-gray-200 bg-gray-50"><div class="text-sm text-gray-700 whitespace-pre-line">{{ $footer }}</div></div>@endif

      <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-xs text-gray-500 no-print"><div class="flex flex-wrap justify-between items-center gap-2"><div>Generated on {{ now()->format('d M Y, H:i') }}</div>@if(!empty($reportId))<div>Report ID: #{{ $reportId }}</div>@endif</div></div>
    </div>
  </div>

  <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded">Skip to main content</a>
</x-app-layout>
</body>
</html>
