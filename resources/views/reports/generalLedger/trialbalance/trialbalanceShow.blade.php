<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $report->title ?? 'Trial Balance' }}
        </h2>
        <p class="text-sm text-gray-600">
            Method:
            <span class="capitalize">{{ $report->method }}</span>
            |
            Period:
            {{ optional($report->from_date)->toDateString() }} -
            {{ optional($report->to_date)->toDateString() }}
        </p>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg border">

                <!-- Action Bar -->
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <div></div>
                    <a href="{{ route('reports.general-ledger.trial-balance.index') }}"
                       class="text-blue-600 hover:text-blue-700 transition">
                        Back to list
                    </a>
                </div>

                <!-- Table -->
                <div class="px-6 py-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Code</th>
                                    <th class="px-4 py-2 text-left">Account</th>
                                    <th class="px-4 py-2 text-right">Debit</th>
                                    <th class="px-4 py-2 text-right">Credit</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($rows as $row)
                                    <tr class="border-t hover:bg-gray-50 transition">
                                        <td class="px-4 py-2">{{ $row->code }}</td>
                                        <td class="px-4 py-2">{{ $row->name }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row->debit, 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row->credit, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="border-t bg-gray-50 font-semibold">
                                    <td class="px-4 py-2" colspan="2">Total</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
