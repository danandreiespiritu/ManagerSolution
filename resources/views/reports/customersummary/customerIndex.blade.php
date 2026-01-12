<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Summary Reports</title>
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

    <div class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Customer Summary Reports</h1>
                            <p class="mt-1 text-sm text-gray-600">View and manage your saved customer summary reports</p>
                        </div>
                        <a href="{{ route('reports.customer-summary.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            New Report
                        </a>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <form method="get" class="flex items-center gap-3">
                        <div class="relative flex-1 max-w-md">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reports by title..." class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        @if(request('q'))
                            <a href="{{ route('reports.customer-summary.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear
                            </a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(($reports ?? collect()) as $report)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                                            <div class="text-sm text-gray-500">Customer Summary Report</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ optional($report->from_date)->format('M j, Y') }} - {{ optional($report->to_date)->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($report->from_date && $report->to_date)
                                            {{ $report->from_date->diffInDays($report->to_date) + 1 }} days
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $report->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $report->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('reports.customer-summary.show', $report) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded-md transition-colors">
                                            <i class="fas fa-eye mr-1.5"></i>
                                            View Report
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">No reports found</h3>
                                        <p class="text-sm text-gray-500 mb-4">
                                            @if(request('q'))
                                                No reports match your search criteria.
                                            @else
                                                Create your first customer summary report to get started.
                                            @endif
                                        </p>
                                        @if(!request('q'))
                                            <a href="{{ route('reports.customer-summary.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>
                                                Create First Report
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($reports) && $reports->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} results
                            </div>
                            <div>{{ $reports->appends(request()->query())->links() }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
