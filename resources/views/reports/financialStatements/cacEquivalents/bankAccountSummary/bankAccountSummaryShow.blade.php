<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bank Account Summary</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif; }
    </style>
    </head>
    <body>
        @include('user.components.navbar')
        <div class="flex min-h-screen bg-gray-50">
            @include('user.components.sidebar')
            <div class="flex-1 flex flex-col p-6">
                <div class="bg-white border border-gray-200 rounded-md shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold">Bank Account Summary</h1>
                            <p class="text-sm text-gray-600">Period: {{ \Carbon\Carbon::parse($report->from_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($report->to_date)->format('M d, Y') }}</p>
                            @if($report->comparative_from && $report->comparative_to)
                                <p class="text-xs text-gray-500">Comparative: {{ \Carbon\Carbon::parse($report->comparative_from)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($report->comparative_to)->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('reports.financial.bank-account-summary.create') }}" class="px-3 py-2 text-sm rounded border border-gray-300 bg-white hover:bg-gray-50">Back</a>
                            <button onclick="window.print()" class="px-3 py-2 text-sm rounded border border-gray-300 bg-white hover:bg-gray-50"><i class="fas fa-print mr-1"></i> Print</button>
                        </div>
                    </div>

                    @if(empty($accountSummaries))
                        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">No bank accounts found for this business.</div>
                    @else
                        @if(count($accountSummaries) > 1)
                            <div class="mt-6">
                                <h2 class="text-lg font-semibold mb-2">All Accounts (Combined)</h2>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Opening</div><div class="text-lg font-bold">{{ number_format($combined['opening'], 2) }}</div></div>
                                    <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Inflows</div><div class="text-lg font-bold text-green-600">{{ number_format($combined['inflows'], 2) }}</div></div>
                                    <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Outflows</div><div class="text-lg font-bold text-red-600">{{ number_format($combined['outflows'], 2) }}</div></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Net Change</div><div class="text-lg font-bold">{{ number_format($combined['inflows'] - $combined['outflows'], 2) }}</div></div>
                                    <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Inter Account Transfers (Net)</div><div class="text-lg font-bold">{{ number_format($combined['transfer_net'], 2) }}</div></div>
                                    <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Closing</div><div class="text-lg font-bold">{{ number_format($combined['closing'], 2) }}</div></div>
                                </div>

                                @if(!empty($combined['outflow_breakdown']))
                                <div class="mt-6">
                                    <h3 class="text-base font-semibold mb-2">Outflow Breakdown</h3>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full border">
                                            <thead>
                                                <tr class="bg-gray-50">
                                                    <th class="text-left p-2 border">P/L Account</th>
                                                    <th class="text-right p-2 border">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($combined['outflow_breakdown'] as $name => $amount)
                                                <tr>
                                                    <td class="p-2 border">{{ $name }}</td>
                                                    <td class="p-2 border text-right">{{ number_format($amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif

                        @foreach($accountSummaries as $s)
                        <div class="mt-8">
                            <h2 class="text-lg font-semibold mb-2">{{ $s['account_name'] }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Opening</div><div class="text-lg font-bold">{{ number_format($s['opening'], 2) }}</div></div>
                                <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Inflows</div><div class="text-lg font-bold text-green-600">{{ number_format($s['inflows'], 2) }}</div></div>
                                <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Outflows</div><div class="text-lg font-bold text-red-600">{{ number_format($s['outflows'], 2) }}</div></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Net Change</div><div class="text-lg font-bold">{{ number_format($s['inflows'] - $s['outflows'], 2) }}</div></div>
                                <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Inter Account Transfers (Net)</div><div class="text-lg font-bold">{{ number_format($s['transfer_net'], 2) }}</div></div>
                                <div class="p-4 border rounded bg-white"><div class="text-gray-500 text-sm">Closing</div><div class="text-lg font-bold">{{ number_format($s['closing'], 2) }}</div></div>
                            </div>
                            @if(!empty($s['outflow_breakdown']))
                            <div class="mt-4">
                                <h3 class="text-base font-semibold mb-2">Outflow Breakdown</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="text-left p-2 border">P/L Account</th>
                                                <th class="text-right p-2 border">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($s['outflow_breakdown'] as $row)
                                            <tr>
                                                <td class="p-2 border">{{ $row->account_name ?? 'Uncategorized' }}</td>
                                                <td class="p-2 border text-right">{{ number_format($row->total ?? 0, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </body>
    </html>
