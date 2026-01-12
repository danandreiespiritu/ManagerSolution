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
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-xl font-semibold text-gray-800 flex items-center">
                        General Ledger Transactions
                        <span class="ml-2 text-gray-400" title="Create a General Ledger report for a selected date range and account.">
                            <i class="far fa-question-circle"></i>
                        </span>
                    </h1>
                </div>

                <!-- Card -->
                <div class="bg-white shadow-sm rounded border border-gray-200">
                    <form action="{{ route('reports.general-ledger.transactions.store') }}" method="POST" class="p-6 space-y-6">
                        @csrf

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input id="description" name="description" type="text"
                                   value="{{ old('description', '') }}"
                                   class="block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="" />
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                                <input id="from_date" name="from_date" type="date"
                                       value="{{ old('from_date', now()->format('Y-m-d')) }}"
                                       class="block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('from_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                                <input id="to_date" name="to_date" type="date"
                                       value="{{ old('to_date', now()->format('Y-m-d')) }}"
                                       class="block w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                @error('to_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Account -->
                        <div class="max-w-md">
                            <label for="account_id" class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                            <select id="account_id" name="account_id"
                                    class="block w-full rounded border-gray-300 bg-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Empty</option>
                                @isset($accounts)
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ ($account->code ?? null) ? ($account->code . ' - ') : '' }}{{ $account->name ?? 'Account' }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('account_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="pt-2 flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow-sm">
                                Create
                            </button>
                            <a href="{{ route('reports.general-ledger.transactions.index') }}" class="text-sm px-3 py-2 rounded border">Back To Reports</a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </body>

</html>