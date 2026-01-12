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
    <style>
        html,
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>
    @include('user.components.navbar')
    <div class="flex min-h-screen bg-gray-50">
        @include('user.components.sidebar')

        <div class="flex-1 p-6">
            <div class="flex-1 p-6">
                <h2 class="text-xl font-semibold mb-4">Supplier Summary</h2>
                <table class="min-w-full table-auto bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Supplier Code</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Supplier Name</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Opening Balance</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse(($data ?? []) as $row)
                            <tr>
                                <td class="border px-4 py-2">{{ $row['supplier_code'] }}</td>
                                <td class="border px-4 py-2">{{ $row['supplier_name'] }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($row['opening_balance'], 2) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($row['closing_balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-4 py-4 text-center text-gray-500">No suppliers found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>