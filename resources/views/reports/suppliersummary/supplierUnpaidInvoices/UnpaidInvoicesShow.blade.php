<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Supplier Statement (Unpaid Invoices)</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #e5e7eb; padding: 8px; font-size: 14px; }
        .table th { background: #f9fafb; text-align: left; }
    </style>
</head>
<body class="bg-gray-50">
@include('user.components.navbar')
<div class="max-w-5xl mx-auto p-6">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold">Statement</h1>
                @if($supplier)
                    <div class="mt-2">
                        <div class="font-semibold">{{ $supplier->name }}</div>
                        <div class="text-gray-600">{{ $supplier->address ?? '' }}</div>
                    </div>
                @endif
            </div>
            <div class="text-sm text-gray-700 flex items-start gap-4">
                <div class="flex gap-10">
                    <div>
                        <div class="font-medium">Date</div>
                        <div>{{ $statementDate->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="font-medium">Address</div>
                        <div class="whitespace-pre-line">{{ $supplier->address ?? '' }}</div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('reports.suppliers.statement-unpaid.export', ['report' => $report->id] + (request()->has('supplier_id') ? ['supplier_id'=>request('supplier_id')] : [])) }}"
                       class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">
                        Export CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="table">
                <thead>
                <tr>
                    <th style="width: 120px;">Date</th>
                    <th style="width: 140px;">Invoice</th>
                    <th>Description</th>
                    <th style="width: 140px;">Invoice total</th>
                    <th style="width: 110px;">Overdue</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 160px;">Balance due</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>{{ optional($inv->issue_date)->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('user.supplier.purchaseInvoice.show', $inv->id) }}" class="text-indigo-600 hover:underline">
                                {{ $inv->reference ?? ('INV-'.$inv->id) }}
                            </a>
                        </td>
                        <td>{{ $inv->description ?? '' }}</td>
                        <td>{{ number_format((float)($inv->grand_total ?? 0), 2) }}</td>
                        <td>{{ $inv->overdue_days > 0 ? $inv->overdue_days.' days' : '—' }}</td>
                        @php $st = strtolower((string)($inv->status ?? '')); $badge = $st === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; @endphp
                        <td><span class="inline-block text-xs px-2 py-1 rounded {{ $badge }}">{{ $st ? ucfirst($st) : '—' }}</span></td>
                        <td>{{ number_format((float)($inv->balance_due ?? ($inv->grand_total ?? 0)), 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500">No unpaid invoices up to {{ $statementDate->format('d/m/Y') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-8">
            <div></div>
            <div>
                <table class="table">
                    <tbody>
                    <tr>
                        <td>Current</td>
                        <td class="text-right">{{ number_format($aging['current'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>1-30 days overdue</td>
                        <td class="text-right">{{ number_format($aging['bucket_1_30'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>31-60 days overdue</td>
                        <td class="text-right">{{ number_format($aging['bucket_31_60'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>61-90 days overdue</td>
                        <td class="text-right">{{ number_format($aging['bucket_61_90'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>90+ days overdue</td>
                        <td class="text-right">{{ number_format($aging['bucket_90_plus'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Total</td>
                        <td class="text-right font-semibold">{{ number_format($aging['total'], 2) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
