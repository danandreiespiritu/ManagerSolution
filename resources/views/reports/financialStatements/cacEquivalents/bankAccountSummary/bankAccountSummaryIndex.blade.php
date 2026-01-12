@php
    // This index mirrors the create page but links to the create action.
@endphp
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

    <div class="flex-1 flex flex-col p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Bank Account Summary</h1>
            <p class="text-gray-600 mt-2">Generate and manage bank account movement summaries</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-chart-bar text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Reports Overview</h2>
                            <p class="text-sm text-gray-500">Track bank account movements and balances</p>
                        </div>
                    </div>
                    <a href="{{ route('reports.financial.bank-account-summary.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Report
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6">
                    <form method="get" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $search ?? '' }}" 
                                   placeholder="Search reports..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" 
                                class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Account</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reports as $r)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-calendar text-green-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ optional($r->from_date)->format('M j, Y') }} - {{ optional($r->to_date)->format('M j, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional($r->from_date)->diffInDays($r->to_date) + 1 }} days
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 p-2 rounded-full mr-3">
                                            <i class="fas fa-university text-blue-600 text-sm"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $r->bankAccount->account_name ?? ($r->bank_account_id ? 'Account #'.$r->bank_account_id : 'All Accounts') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $r->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('reports.financial.bank-account-summary.show', $r) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg transition-colors">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="{{ route('reports.financial.bank-account-summary.edit', $r) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg transition-colors">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-gray-100 p-4 rounded-full mb-4">
                                            <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No reports found</h3>
                                        <p class="text-gray-500 mb-4">Get started by creating your first bank account summary report.</p>
                                        <a href="{{ route('reports.financial.bank-account-summary.create') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                                            <i class="fas fa-plus mr-2"></i>Create Report
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $reports->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
