<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Receipts & Payments Summary</title>
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

        <div class="flex-1 flex flex-col">
            <main class="flex-1 p-6">
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 id="pageHeading" class="text-2xl font-semibold text-gray-800">Receipts & Payments Summary</h1>
                        <p class="text-sm text-gray-500">Create a new Receipts & Payments Summary</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-arrow-left"></i>
                            <span class="text-sm">Back</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <form action="{{ route('reports.financial.receipts-payments-summary.store') }}" method="POST" class="p-6 space-y-6 max-w-3xl">
                        @csrf

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input id="title" type="text" name="title" placeholder="Receipts & Payments Summary" value="{{ old('title', 'Receipts & Payments Summary') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <input id="description" type="text" name="description" placeholder="Optional" value="{{ old('description') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div id="columnsContainer" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl">
                                    <div class="sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">From</label>
                                        <div class="relative mt-1">
                                            <input id="from" type="date" name="from" value="{{ old('from', '2025-07-10') }}" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                            <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">To</label>
                                        <div class="relative mt-1">
                                            <input id="to" type="date" name="to" value="{{ old('to', '2025-07-10') }}" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                            <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-4">
                                        <label for="column_label" class="block text-sm font-medium text-gray-700">Column name</label>
                                        <input id="column_label" type="text" name="column_label" placeholder="Automatic" value="{{ old('column_label', 'Automatic') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <button type="button" class="inline-flex items-center justify-center h-10 w-10 border rounded-md text-gray-600 hover:bg-gray-50" title="Calendar">
                                            <i class="far fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <button id="addComparativeBtn" type="button" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                                    Add comparative column
                                    <i class="fas fa-caret-down"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="footer" class="block text-sm font-medium text-gray-700">Footer</label>
                            <textarea id="footer" name="footer" rows="6" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder=""></textarea>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="exclude_zero_balances" name="exclude_zero_balances" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('exclude_zero_balances') ? 'checked' : '' }}>
                                <label for="exclude_zero_balances" class="ml-2 block text-sm text-gray-700">
                                    Exclude zero balances
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="show_account_codes" name="show_account_codes" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('show_account_codes') ? 'checked' : '' }}>
                                <label for="show_account_codes" class="ml-2 block text-sm text-gray-700">
                                    Show account codes
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex justify-start">
                            <button id="submitBtn" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">Create</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
<script>
    (function() {
        const columnsContainer = document.getElementById('columnsContainer');
        const addBtn = document.getElementById('addComparativeBtn');
        const fromMain = document.getElementById('from');
        const toMain = document.getElementById('to');
        const submitBtn = document.getElementById('submitBtn');

        let compIndex = 0;

        function buildComparativeRow(index) {
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl';
            row.innerHTML = `
            <div class="sm:col-span-4">
                <label class="block text-sm font-medium text-gray-700">From</label>
                <div class="relative mt-1">
                    <input type="date" name="comparatives[${index}][from]" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                    <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
            <div class="sm:col-span-4">
                <label class="block text-sm font-medium text-gray-700">To</label>
                <div class="relative mt-1">
                    <input type="date" name="comparatives[${index}][to]" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                    <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                </div>
            </div>
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700">Column name</label>
                <input type="text" name="comparatives[${index}][label]" placeholder="Optional" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
            </div>
            <div class="sm:col-span-1 flex items-end">
                <button type="button" class="remove-comp mt-1 inline-flex items-center justify-center h-10 w-10 border rounded-md text-gray-600 hover:bg-gray-50" title="Remove column">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>`;
            row.querySelector('.remove-comp').addEventListener('click', () => row.remove());
            return row;
        }

        addBtn?.addEventListener('click', () => {
            const row = buildComparativeRow(compIndex++);
            columnsContainer.appendChild(row);
        });

        function validate() {
            // Ensure from <= to on main range
            let ok = true;
            const from = fromMain?.value ? new Date(fromMain.value) : null;
            const to = toMain?.value ? new Date(toMain.value) : null;
            if (from && to && from > to) ok = false;

            if (submitBtn) {
                submitBtn.disabled = !ok;
                submitBtn.classList.toggle('opacity-50', !ok);
                submitBtn.classList.toggle('cursor-not-allowed', !ok);
            }
            return ok;
        }
        fromMain?.addEventListener('change', validate);
        toMain?.addEventListener('change', validate);
        validate();
    })();
</script>