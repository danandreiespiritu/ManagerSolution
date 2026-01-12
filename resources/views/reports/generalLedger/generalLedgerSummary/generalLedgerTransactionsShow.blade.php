<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>General Ledger Transactions</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@include('user.components.navbar')
<div class="flex min-h-screen bg-gray-50">
  @include('user.components.sidebar')
  <main class="flex-1 p-6">
    <div class="max-w-5xl mx-auto">
      <div class="bg-white shadow-sm rounded border">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <div>
            <h1 class="text-xl font-semibold">General Ledger Transactions</h1>
            <p class="text-sm text-gray-600">Period: {{ $from ?? '—' }} - {{ $to ?? '—' }}@if($accountFilterName) | Account: {{ $accountFilterName }}@endif</p>
          </div>
          <a href="{{ route('reports.general-ledger.transactions.index') }}" class="text-blue-600">Back to list</a>
        </div>
        <div class="px-6 py-6">
          <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-4 py-2 text-left">Date</th>
                  <th class="px-4 py-2 text-left">Code</th>
                  <th class="px-4 py-2 text-left">Account</th>
                  <th class="px-4 py-2 text-left">Reference</th>
                  <th class="px-4 py-2 text-left">Narration</th>
                  <th class="px-4 py-2 text-right">Debit</th>
                  <th class="px-4 py-2 text-right">Credit</th>
                  <th class="px-4 py-2 text-center">Status</th>
                  <th class="px-4 py-2 text-left">Link</th>
                </tr>
              </thead>
              <tbody>
                @forelse($entries as $e)
                  <tr class="border-t">
                    <td class="px-4 py-2">{{ optional($e->date)->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $accountCodes[$e->account_id] ?? '' }}</td>
                    <td class="px-4 py-2">{{ $accountNames[$e->account_id] ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $e->reference ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $e->narration ?? '—' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format((float)($e->debit ?? 0), 2) }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format((float)($e->credit ?? 0), 2) }}</td>
                    <td class="px-4 py-2 text-center">
                      @php
                        // use precomputed entryStatus if present, otherwise fallback to computing per-entry sums
                        $status = $entryStatus[$e->entry_id] ?? null;
                      @endphp

                      @if(!$status)
                        @php
                          // fallback calculation (safety): compute sums for this entry_id from $entries collection
                          $sumDebit = $entries->where('entry_id', $e->entry_id)->sum(function($it){ return (float)($it->debit ?? 0); });
                          $sumCredit = $entries->where('entry_id', $e->entry_id)->sum(function($it){ return (float)($it->credit ?? 0); });
                          $status = (abs($sumDebit - $sumCredit) <= 0.01) ? 'Balanced' : 'Unbalanced';
                        @endphp
                      @endif

                      @if($status === 'Balanced')
                        <span class="text-green-600 font-medium">Balanced</span>
                      @else
                        <span class="text-red-600 font-medium">Unbalanced</span>
                      @endif
                    </td>
                    <td class="px-4 py-2"><a class="text-indigo-600" href="{{ route('journal_entries.edit', $e->entry_id) }}">Open</a></td>
                  </tr>
                @empty
                  <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">No entries for the selected criteria.</td></tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr class="border-t bg-gray-50 font-semibold">
                  <td class="px-4 py-2" colspan="5">Total</td>
                  <td class="px-4 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                  <td class="px-4 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                  <td class="px-4 py-2 text-center">
                    @if(abs($totalDebit - $totalCredit) <= 0.01)
                      <span class="text-green-600 font-medium">Balanced</span>
                    @else
                      <span class="text-red-600 font-medium">Unbalanced</span>
                    @endif
                  </td>
                  <td class="px-4 py-2"></td>
                </tr>
                <tr class="border-t bg-gray-50 font-semibold">
                  <td class="px-4 py-2" colspan="5">Balance</td>
                  <td class="px-4 py-2 text-right" colspan="2">{{ number_format($balance, 2) }}</td>
                  <td class="px-4 py-2 text-center">
                    @if(abs($totalDebit - $totalCredit) <= 0.01)
                      <span class="text-green-600 font-medium">Balanced</span>
                    @else
                      <span class="text-red-600 font-medium">Unbalanced</span>
                    @endif
                  </td>
                  <td class="px-4 py-2"></td>
                </tr>
              </tfoot>
            </table>
            @isset($report)
              <div class="mt-4">
                <a href="{{ route('reports.general-ledger.transactions.export', $report) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded text-sm">Export CSV</a>
              </div>
            @endisset
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>