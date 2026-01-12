<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports</title>
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
            html,
            body {
                font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', sans-serif;
            }
        </style>
    </head>

    <body>
        @include('user.components.navbar')
        <div class="flex min-h-screen bg-gray-50">
            <!-- Sidebar -->
            @include('user.components.sidebar')

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <h1 class="text-base md:text-lg font-semibold text-gray-900">General Ledger Summary</h1>
                                <span class="inline-flex items-center justify-center h-5 w-5 text-gray-400 border border-gray-200 rounded-full text-xs" title="Generate general ledger summary">?</span>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="px-6 py-6">
                            <form action="{{ route('reports.general-ledger.summary.store') }}" method="POST" class="space-y-6 max-w-3xl">
                                @csrf

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <input type="text" id="description" name="description" value="{{ old('description') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
                                </div>

                                <!-- Period -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                        <input type="date" id="from_date" name="from_date" value="{{ old('from_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                        <input type="date" id="to_date" name="to_date" value="{{ old('to_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <!-- Options -->
                                <div class="space-y-3">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" name="show_codes" value="1" class="form-checkbox text-indigo-600 rounded" {{ old('show_codes') ? 'checked' : '' }}>
                                        <span class="text-gray-700 text-sm">Show account codes</span>
                                    </label>
                                    <div>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="exclude_zero" value="1" class="form-checkbox text-indigo-600 rounded" {{ old('exclude_zero') ? 'checked' : '' }}>
                                            <span class="text-gray-700 text-sm">Exclude zero balances</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">
                                        Create
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>

</html>