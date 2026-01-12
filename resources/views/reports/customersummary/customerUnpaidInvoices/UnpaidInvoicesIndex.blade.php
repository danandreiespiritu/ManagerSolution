<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Unpaid Invoices</title>
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
        <div class="max-w-5xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h1 class="text-xl font-semibold">Customer Statements (Unpaid Invoices)</h1>
                    <a href="{{ route('reports.customers.statement-unpaid') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        <i class="fas fa-plus mr-2"></i> New Statement
                    </a>
                </div>

                <div class="p-6">
                    @if(isset($reports) && $reports->count())
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-3 py-2">Title</th>
                                    <th class="text-left px-3 py-2">Statement Date</th>
                                    <th class="text-left px-3 py-2">Created</th>
                                    <th class="text-left px-3 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($reports as $report)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $report->title }}</td>
                                        <td class="px-3 py-2">{{ optional($report->statement_date)->format('M j, Y') }}</td>
                                        <td class="px-3 py-2">{{ $report->created_at->format('M j, Y g:i A') }}</td>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('reports.customers.statement-unpaid.show', $report) }}" class="text-blue-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $reports->links() }}</div>
                    @else
                        <div class="text-center text-gray-500 py-10">No statements yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
