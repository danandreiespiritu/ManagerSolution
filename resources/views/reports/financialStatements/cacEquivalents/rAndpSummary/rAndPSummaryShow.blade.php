<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $report->title }}</title>
    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind -->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
        }
        @media print {
            .no-print { display: none !important; }
            .print-container { margin: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    @include('user.components.navbar')
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('user.components.sidebar')

        <div class="flex-1 flex flex-col">
            <main class="flex-1 p-6 print-container">
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $report->title }}</h1>
                        <p class="text-sm text-gray-500">{{ $report->description ?? 'Receipts & Payments Summary Report' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-print"></i>
                            <span class="text-sm">Print</span>
                        </button>
                        <a href="{{ route('reports.financial.receipts-payments-summary.create') }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-arrow-left"></i>
                            <span class="text-sm">Back</span>
                        </a>
                    </div>
                </div>

                <!-- Report Content -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8">
                    <!-- Report Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">{{ $report->business->name ?? 'dsadsa' }}</h2>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $report->title }}</h3>
                        <p class="text-sm text-gray-600">
                            For the period from {{ $report->from_date->format('m/d/Y') }} to {{ $report->to_date->format('m/d/Y') }}
                        </p>
                        <div class="flex justify-end mt-4">
                            <span class="text-sm text-gray-600">{{ $report->to_date->format('m/d/Y') }}</span>
                        </div>
                    </div>

                    <hr class="border-gray-400 mb-6">

                    <!-- Receipts Section -->
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-900 mb-4">Receipts</h4>
                        <div class="space-y-2">
                            @forelse($receiptsData as $receipt)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">
                                        @if($report->show_account_codes && $receipt->account_code)
                                            {{ $receipt->account_code }} - 
                                        @endif
                                        {{ $receipt->account }}
                                    </span>
                                    <span class="text-gray-700">{{ number_format($receipt->total_amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Accounting fees</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Bank charges</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">E</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Legal fees</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Sales</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Suspense</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                            @endforelse
                        </div>
                        <hr class="border-gray-400 my-4">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-gray-900">Total — Receipts</span>
                            <span class="text-gray-900">{{ number_format($totalReceipts, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payments Section -->
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-900 mb-4">Less: Payments</h4>
                        <div class="space-y-2">
                            @forelse($paymentsData as $payment)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">
                                        @if($report->show_account_codes && $payment->account_code)
                                            {{ $payment->account_code }} - 
                                        @endif
                                        {{ $payment->account }}
                                    </span>
                                    <span class="text-gray-700">{{ number_format($payment->total_amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Accounting fees</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Bank charges</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">E</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Legal fees</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Sales</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Suspense</span>
                                    <span class="text-gray-700">-</span>
                                </div>
                            @endforelse
                        </div>
                        <hr class="border-gray-400 my-4">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-gray-900">Total — Payments</span>
                            <span class="text-gray-900">{{ number_format($totalPayments, 2) }}</span>
                        </div>
                    </div>

                    <hr class="border-gray-400 my-6">

                    <!-- Net Cash Change -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-gray-900">Net increase (decrease) in cash held</span>
                            <span class="text-gray-900">{{ number_format($netCashChange, 2) }}</span>
                        </div>
                    </div>

                    <hr class="border-gray-400 my-6">

                    <!-- Cash Balances -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Cash at the beginning of the period</span>
                            <span class="text-gray-700">({{ number_format(abs($cashAtBeginning), 2) }})</span>
                        </div>

                        <hr class="border-gray-400 my-4">

                        <div class="flex justify-between items-center font-bold">
                            <span class="text-gray-900">Cash at the end of the period</span>
                            <span class="text-gray-900">({{ number_format(abs($cashAtEnd), 2) }})</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    @if($report->footer)
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="text-sm text-gray-600 whitespace-pre-line">{{ $report->footer }}</div>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
</body>
</html>