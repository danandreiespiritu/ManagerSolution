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
                                <h1 class="text-xl font-semibold text-gray-900">Trial Balance</h1>
                                <span class="inline-flex items-center justify-center h-6 w-6 text-gray-400 border border-gray-200 rounded-full text-xs" title="Generate supplier summary report">?</span>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="px-6 py-6">
                            <form action="{{ route('reports.general-ledger.trial-balance.store') }}" method="POST" class="space-y-6 max-w-3xl">
                                @csrf

                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                    <input type="text" id="title" name="title" value="{{ old('title', 'Trial Balance') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Trial Balance">
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <input type="text" id="description" name="description" value="{{ old('description') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
                                </div>

                                <!-- Accounting method -->
                                <div>
                                    <label for="accounting_method" class="block text-sm font-medium text-gray-700 mb-2">Accounting method</label>
                                    <select id="accounting_method" name="accounting_method" class="w-full md:w-60 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="accrual" {{ old('accounting_method', 'accrual') === 'accrual' ? 'selected' : '' }}>Accrual basis</option>
                                        <option value="cash" {{ old('accounting_method') === 'cash' ? 'selected' : '' }}>Cash basis</option>
                                    </select>
                                </div>

                                <!-- Period and Column name -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div>
                                        <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                        <input type="date" id="from_date" name="from_date" value="{{ old('from_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                        <input type="date" id="to_date" name="to_date" value="{{ old('to_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label for="column_name" class="block text-sm font-medium text-gray-700 mb-2">Column name</label>
                                        <div class="flex">
                                            <input type="text" id="column_name" name="column_name" value="{{ old('column_name') }}" placeholder="Automatic" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" disabled>
                                            <button type="button" class="px-3 border border-l-0 border-gray-300 rounded-r-md bg-gray-100 text-gray-600" title="Automatic">
                                                <i class="fas fa-th"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Comparative column (dynamic rows) -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="add-comparative-column" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-sm text-gray-700 hover:bg-gray-50">
                                            Add comparative column
                                            <i class="fas fa-caret-down ml-2"></i>
                                        </button>
                                    </div>

                                    <!-- Container for added comparative column rows -->
                                    <div id="comparative-columns-container" class="space-y-3"></div>

                                    <!-- Template for a comparative column row -->
                                    <template id="comparative-column-template">
                                        <div class="flex flex-col md:flex-row md:items-end md:gap-4 bg-gray-50 p-3 rounded border border-gray-200">
                                            <div class="flex-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                                                <input type="text" name="comparative_columns[][label]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Comparative column name">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                                <input type="date" name="comparative_columns[][from_date]" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                                <input type="date" name="comparative_columns[][to_date]" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <div class="md:pt-6">
                                                <button type="button" class="comparative-remove inline-flex items-center px-3 py-2 border border-red-300 rounded-md bg-red-50 text-sm text-red-700 hover:bg-red-100">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </template>
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
    <script>
        (function(){
            // Elements
            const addBtn = document.getElementById('add-comparative-column');
            const container = document.getElementById('comparative-columns-container');
            const template = document.getElementById('comparative-column-template');

            if (!addBtn || !container || !template) return;

            function attachRemove(btn){
                btn.addEventListener('click', function(){
                    const row = btn.closest('div.flex') || btn.closest('div');
                    if (row) row.remove();
                });
            }

            addBtn.addEventListener('click', function(){
                const node = template.content.cloneNode(true);
                // Append and attach remove handler
                container.appendChild(node);
                // Attach remove listeners for newly added remove buttons
                container.querySelectorAll('.comparative-remove').forEach(btn => {
                    // Only attach once per button
                    if (!btn.dataset.bound) {
                        btn.dataset.bound = '1';
                        attachRemove(btn);
                    }
                });
            });

            // Optional: allow removing rows by delegating to the container
            container.addEventListener('click', function(e){
                if (e.target && e.target.classList && e.target.classList.contains('comparative-remove')){
                    const btn = e.target;
                    const row = btn.closest('div.flex') || btn.closest('div');
                    if (row) row.remove();
                }
            });
        })();
    </script>
</html>