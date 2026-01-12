<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Supplier Statements (Transactions)</title>
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
<div class="flex min-h-screen">
    @include('user.components.sidebar')
    <main class="flex-1 p-6">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Supplier Statements (Transactions)</h1>
                        <p class="mt-1 text-sm text-gray-600">Saved statements for your active business</p>
                    </div>
                    <a href="{{ route('reports.suppliers.statement-transactions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                        <i class="fas fa-plus mr-2"></i>New Statement
                    </a>
                </div>
                <div class="px-6 py-4">
                    <form method="GET" class="mb-4">
                        <div class="flex gap-2 items-center">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title..." class="px-3 py-2 border border-gray-300 rounded w-64"/>
                            <button class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded border border-gray-300">Search</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left border-b">Title</th>
                                    <th class="px-3 py-2 text-left border-b">From</th>
                                    <th class="px-3 py-2 text-left border-b">To</th>
                                    <th class="px-3 py-2 text-left border-b">Supplier</th>
                                    <th class="px-3 py-2 text-right border-b w-40">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr class="border-b">
                                        <td class="px-3 py-2">{{ $report->title ?? 'Supplier Statement (Transactions)' }}</td>
                                        <td class="px-3 py-2">{{ optional($report->from_date)->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">{{ optional($report->to_date)->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">{{ optional($report->supplier)->name ?? 'All' }}</td>
                                        <td class="px-3 py-2 text-right">
                                            <a href="{{ route('reports.suppliers.statement-transactions.show', $report) }}" class="text-blue-600 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">No saved statements yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $reports->links() }}</div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>