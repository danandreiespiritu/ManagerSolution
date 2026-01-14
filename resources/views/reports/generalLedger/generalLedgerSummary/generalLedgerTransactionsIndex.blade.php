<x-app-layout>
<div class="flex min-h-screen bg-gray-50">
  <main class="flex-1 p-6">
    <div class="max-w-5xl mx-auto">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">General Ledger Transactions</h1>
        <a href="{{ route('reports.general-ledger.transactions') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create</a>
      </div>
      <div class="bg-white rounded border">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left">Title</th>
              <th class="px-4 py-2 text-left">From</th>
              <th class="px-4 py-2 text-left">To</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports as $r)
              <tr class="border-t">
                <td class="px-4 py-2">{{ $r->title ?? 'General Ledger Transactions' }}</td>
                <td class="px-4 py-2">{{ optional($r->from_date)->toDateString() }}</td>
                <td class="px-4 py-2">{{ optional($r->to_date)->toDateString() }}</td>
                <td class="px-4 py-2 text-right"><a class="text-blue-600" href="{{ route('reports.general-ledger.transactions.showSaved', $r) }}">View</a></td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No reports yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>
</x-app-layout>
