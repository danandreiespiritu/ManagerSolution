<x-app-layout>
    <x-slot name="title">
        General Ledger Summary
    </x-slot>

    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold">General Ledger Accounts</h1>
            <p class="text-sm text-gray-600">{{ optional($report->business)->business_name }}</p>
            <p class="text-sm text-gray-600">December 31, {{ optional($report->to_date)->year ?? '2011' }}</p>
            <p class="text-sm text-gray-500 italic">Amounts in Philippine Pesos</p>

            @if($report->description)
                <p class="text-sm text-gray-500 mt-1">{{ $report->description }}</p>
            @endif
        </div>

        <div class="flex gap-2 no-print">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i>Print
            </button>

            <a href="{{ route('reports.general-ledger.summary.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Back to list
            </a>
        </div>
    </x-slot>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body { background: white; }
            .page-break { page-break-before: always; }
        }
        .ledger-table {
            border-collapse: collapse;
            width: 100%;
            font-size: 0.875rem;
        }
        .ledger-table th,
        .ledger-table td {
            border: 1px solid #000;
            padding: 4px 8px;
        }
        .ledger-table th {
            background-color: #f3f4f6;
            font-weight: 600;
            text-align: center;
        }
    </style>

    <div class="bg-white shadow-sm rounded border p-6">
        @foreach($accounts as $index => $account)
            <div class="mb-10 {{ $index > 0 ? 'page-break' : '' }}">
                <h2 class="text-lg font-bold mb-2">GENERAL LEDGER</h2>

                <div class="flex justify-between items-center border-b-2 border-black pb-1 mb-1">
                    <div>
                        <span class="font-semibold">Account Name: </span>
                        <span class="uppercase">{{ $account->name }}</span>
                    </div>
                </div>

                <div class="border-b-2 border-black pb-2 mb-4">
                    <span class="font-semibold">Account Number: </span>
                    <span>{{ $account->code ?? '001' }}</span>
                </div>

                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 35%;">Explanation</th>
                            <th style="width: 8%;">Ref</th>
                            <th style="width: 15%;">Debit</th>
                            <th style="width: 15%;">Credit</th>
                            <th style="width: 15%;">Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(count($account->lines) > 0)
                            @foreach($account->lines as $line)
                                <tr>
                                    <td class="text-center">
                                        {{ $line->date ? \Carbon\Carbon::parse($line->date)->format('m/d/Y') : '' }}
                                    </td>
                                    <td>{{ $line->explanation }}</td>
                                    <td class="text-center">{{ $line->ref }}</td>
                                    <td class="text-right">{{ $line->debit }}</td>
                                    <td class="text-right">{{ $line->credit }}</td>
                                    <td class="text-right">{{ number_format($line->balance, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center text-gray-500">
                                    No transactions for this period
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endforeach

        @if(count($accounts) == 0)
            <div class="text-center py-8 text-gray-500">
                <p>No accounts with activity for the selected period.</p>
            </div>
        @endif
    </div>

</x-app-layout>
