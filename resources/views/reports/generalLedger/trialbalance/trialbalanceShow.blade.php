<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $report->title ?? 'Trial Balance' }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
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
                        <h1 class="text-xl font-semibold">{{ $report->title ?? 'Trial Balance' }}</h1>
                        <p class="text-sm text-gray-600">Method: <span class="capitalize">{{ $report->method }}</span> | Period: {{ optional($report->from_date)->toDateString() }} - {{ optional($report->to_date)->toDateString() }}</p>
                    </div>
                    <a href="{{ route('reports.general-ledger.trial-balance.index') }}" class="text-blue-600">Back to list</a>
                </div>
                <div class="px-6 py-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Code</th>
                                    <th class="px-4 py-2 text-left">Account</th>
                                    <th class="px-4 py-2 text-right">Debit</th>
                                    <th class="px-4 py-2 text-right">Credit</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $row->code }}</td>
                                        <td class="px-4 py-2">{{ $row->name }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row->debit, 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($row->credit, 2) }}</td>
                                        <td class="px-4 py-2 text-center">
                                            @if($row->status === 'Balanced')
                                                <span class="text-green-600 font-medium">Balanced</span>
                                            @else
                                                <span class="text-red-600 font-medium">Unbalanced</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t bg-gray-50 font-semibold">
                                    <td class="px-4 py-2" colspan="2">Total</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                                    <td class="px-4 py-2 text-center">
                                        @if(abs($totalDebit - $totalCredit) <= 0.01)
                                            <span class="text-green-600 font-medium">Balanced</span>
                                        @else
                                            <span class="text-red-600 font-medium">Unbalanced</span>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>