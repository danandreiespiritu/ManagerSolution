<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Receipts & Payments Summary Reports</title>
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
    </style>
</head>
<body>
    @include('user.components.navbar')
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        @include('user.components.sidebar')

        <div class="flex-1 flex flex-col">
            <main class="flex-1 p-6">
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800">Receipts & Payments Summary Reports</h1>
                        <p class="text-sm text-gray-500">Manage your receipts and payments summary reports</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('reports.financial.receipts-payments-summary.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">
                            <i class="fas fa-plus"></i>
                            <span>New Report</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    @if($reports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reports as $report)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                                                @if($report->description)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($report->description, 50) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->from_date->format('M d, Y') }} - {{ $report->to_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->user->name ?? 'Unknown' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report->created_at->format('M d, Y g:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('reports.financial.receipts-payments-summary.show', $report) }}" class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('reports.financial.receipts-payments-summary.destroy', $report) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this report?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($reports->hasPages())
                            <div class="px-6 py-3 border-t border-gray-200">
                                {{ $reports->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Reports Found</h3>
                            <p class="text-gray-500 mb-4">You haven't created any receipts & payments summary reports yet.</p>
                            <a href="{{ route('reports.financial.receipts-payments-summary.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">
                                <i class="fas fa-plus"></i>
                                <span>Create Your First Report</span>
                            </a>
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    @if(session('success'))
        <div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 3000);
        </script>
    @endif
</body>
</html>