<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $report->title }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        <div class="max-w-6xl mx-auto space-y-6">
            <div class="bg-white border rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Customer Statement (Transactions)</h2>
                    <div class="text-sm text-gray-500">{{ $from->format('M j, Y') }} - {{ $to->format('M j, Y') }}</div>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                    <span class="font-medium">Customer:</span>
                    {{ $customer?->name ?? 'All customers' }}
                </div>
            </div>

            <div class="bg-white border rounded-lg p-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-3 py-2">Date</th>
                            <th class="text-left px-3 py-2">Type</th>
                            <th class="text-left px-3 py-2">Reference</th>
                            <th class="text-left px-3 py-2">Description</th>
                            <th class="text-right px-3 py-2">Debit</th>
                            <th class="text-right px-3 py-2">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($entries as $e)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2">{{ optional($e->date)->format('M j, Y') }}</td>
                                <td class="px-3 py-2">{{ $e->type }}</td>
                                <td class="px-3 py-2">{{ $e->reference }}</td>
                                <td class="px-3 py-2">{{ $e->description }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format((float)($e->debit ?? 0), 2) }}</td>
                                <td class="px-3 py-2 text-right">{{ number_format((float)($e->credit ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-gray-500">No transactions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-right">Totals</td>
                            <td class="px-3 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-right">Balance</td>
                            <td colspan="2" class="px-3 py-2 text-right">{{ number_format($balance, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
