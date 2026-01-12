<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Supplier Statement (Transactions)</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
@include('user.components.navbar')
<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">Statement</h1>
                @if($supplier)
                    <div class="mt-2">
                        <div class="font-semibold">{{ $supplier->name }}</div>
                        <div class="text-gray-600">{{ $supplier->address ?? '' }}</div>
                    </div>
                @endif
            </div>
            <div class="text-sm text-gray-700">
                <div class="font-medium">Period</div>
                <div>{{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left border-b">Date</th>
                    <th class="px-3 py-2 text-left border-b">Type</th>
                    <th class="px-3 py-2 text-left border-b">Reference</th>
                    <th class="px-3 py-2 text-left border-b">Description</th>
                    <th class="px-3 py-2 text-right border-b">Debit</th>
                    <th class="px-3 py-2 text-right border-b">Credit</th>
                </tr>
                </thead>
                <tbody>
                @forelse($entries as $e)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ optional($e->date)->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">{{ $e->type }}</td>
                        <td class="px-3 py-2">{{ $e->reference }}</td>
                        <td class="px-3 py-2">{{ $e->description }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format((float)($e->debit ?? 0), 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format((float)($e->credit ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">No transactions in this period.</td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="4" class="px-3 py-2 text-right">Totals</td>
                    <td class="px-3 py-2 text-right">{{ number_format($totalDebit, 2) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($totalCredit, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="px-3 py-2 text-right font-semibold">Balance</td>
                    <td colspan="2" class="px-3 py-2 text-right font-semibold">{{ number_format($balance, 2) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>