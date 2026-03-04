<x-app-layout>
<div class="min-h-screen bg-gray-50 py-8">
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-200 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Cash Journal Entry Transactions</h1>
          <p class="mt-1 text-sm text-gray-600">
            Period: {{ $from ?? '—' }} to {{ $to ?? '—' }}@if($accountFilterName) • Account: {{ $accountFilterName }}@endif
          </p>
        </div>
        <div class="flex items-center gap-3">
          @isset($report)
            <a href="{{ route('reports.general-ledger.transactions.export', $report) }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
              Export CSV
            </a>
          @endisset
          <a href="{{ route('reports.general-ledger.transactions.index') }}"
             class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            Back to List
          </a>
        </div>
      </div>

      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
          <div class="bg-white border border-gray-200 rounded-lg px-4 py-3">
            <div class="text-gray-500">Total Debit</div>
            <div class="mt-1 font-semibold text-gray-900 text-base">{{ number_format((float)$totalDebit, 2) }}</div>
          </div>
          <div class="bg-white border border-gray-200 rounded-lg px-4 py-3">
            <div class="text-gray-500">Total Credit</div>
            <div class="mt-1 font-semibold text-gray-900 text-base">{{ number_format((float)$totalCredit, 2) }}</div>
          </div>
          <div class="bg-white border border-gray-200 rounded-lg px-4 py-3">
            <div class="text-gray-500">Net Balance</div>
            <div class="mt-1 font-semibold {{ abs($balance) <= 0.01 ? 'text-emerald-700' : 'text-amber-700' }} text-base">{{ number_format((float)$balance, 2) }}</div>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap">Date</th>
              <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap">Entry #</th>
              <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap">Account Code</th>
              <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap">Cash Account</th>
              <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Reference</th>
              <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700">Narration</th>
              <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 whitespace-nowrap">Debit</th>
              <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 whitespace-nowrap">Credit</th>
              <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-700 whitespace-nowrap">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($entries as $e)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-800 whitespace-nowrap">
                  {{ !empty($e->date) ? \Illuminate\Support\Carbon::parse($e->date)->format('Y-m-d') : '—' }}
                </td>
                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $e->entry_id ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $accountCodes[$e->account_id] ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-900 font-medium">{{ $accountNames[$e->account_id] ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $e->reference ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $e->narration ?? '—' }}</td>
                <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">{{ number_format((float)($e->debit ?? 0), 2) }}</td>
                <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">{{ number_format((float)($e->credit ?? 0), 2) }}</td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                  @php($status = $entryStatus[$e->entry_id] ?? null)
                  @if($status === 'Balanced')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Balanced</span>
                  @elseif($status)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ $status }}</span>
                  @else
                    <span class="text-gray-400">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-4 py-10 text-center text-gray-500">No cash journal transactions found for the selected criteria.</td>
              </tr>
            @endforelse
          </tbody>
          <tfoot class="bg-gray-50 border-t border-gray-200">
            <tr>
              <td class="px-4 py-3 font-semibold text-gray-800" colspan="6">Totals</td>
              <td class="px-4 py-3 text-right font-semibold text-gray-900 tabular-nums">{{ number_format((float)$totalDebit, 2) }}</td>
              <td class="px-4 py-3 text-right font-semibold text-gray-900 tabular-nums">{{ number_format((float)$totalCredit, 2) }}</td>
              <td class="px-4 py-3 text-center font-semibold {{ abs($totalDebit - $totalCredit) <= 0.01 ? 'text-emerald-700' : 'text-amber-700' }}">
                {{ abs($totalDebit - $totalCredit) <= 0.01 ? 'Balanced' : 'Unbalanced' }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </main>
</div>
</x-app-layout>