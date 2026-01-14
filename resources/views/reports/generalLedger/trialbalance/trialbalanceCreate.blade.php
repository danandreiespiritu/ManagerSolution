<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trial Balance
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-gray-900">Trial Balance</h1>
                    <span class="inline-flex items-center justify-center h-6 w-6 text-gray-400 border border-gray-200 rounded-full text-xs" title="Generate supplier summary report">?</span>
                </div>

                <!-- Form -->
                <div class="px-6 py-6">
                    <form action="{{ route('reports.general-ledger.trial-balance.store') }}" method="POST" class="space-y-6 max-w-3xl">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" id="title" name="title" value="{{ old('title', 'Trial Balance') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <input type="text" id="description" name="description" value="{{ old('description') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Accounting Method -->
                        <div>
                            <label for="accounting_method" class="block text-sm font-medium text-gray-700 mb-2">Accounting method</label>
                            <select id="accounting_method" name="accounting_method"
                                    class="w-full md:w-60 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="accrual" {{ old('accounting_method', 'accrual') === 'accrual' ? 'selected' : '' }}>Accrual basis</option>
                                <option value="cash" {{ old('accounting_method') === 'cash' ? 'selected' : '' }}>Cash basis</option>
                            </select>
                        </div>

                        <!-- Period and Column name -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                <input type="date" id="from_date" name="from_date"
                                       value="{{ old('from_date', date('Y-m-d')) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                <input type="date" id="to_date" name="to_date"
                                       value="{{ old('to_date', date('Y-m-d')) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="column_name" class="block text-sm font-medium text-gray-700 mb-2">Column name</label>
                                <div class="flex">
                                    <input type="text" id="column_name" name="column_name"
                                           value="{{ old('column_name') }}"
                                           placeholder="Automatic" disabled
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button"
                                            class="px-3 border border-l-0 border-gray-300 rounded-r-md bg-gray-100 text-gray-600"
                                            title="Automatic">
                                        <i class="fas fa-th"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Comparative column dynamic rows -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <button type="button" id="add-comparative-column"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md bg-white text-sm text-gray-700 hover:bg-gray-50">
                                    Add comparative column
                                    <i class="fas fa-caret-down ml-2"></i>
                                </button>
                            </div>

                            <div id="comparative-columns-container" class="space-y-3"></div>

                            <template id="comparative-column-template">
                                <div class="flex flex-col md:flex-row md:items-end md:gap-4 bg-gray-50 p-3 rounded border border-gray-200">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                                        <input type="text" name="comparative_columns[][label]"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">From</label>
                                        <input type="date" name="comparative_columns[][from_date]"
                                               class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                                        <input type="date" name="comparative_columns[][to_date]"
                                               class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div class="md:pt-6">
                                        <button type="button"
                                            class="comparative-remove inline-flex items-center px-3 py-2 border border-red-300 rounded-md bg-red-50 text-sm text-red-700 hover:bg-red-100">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">
                                Create
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
            (function(){
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
                    container.appendChild(node);
                    container.querySelectorAll('.comparative-remove').forEach(btn => {
                        if (!btn.dataset.bound) {
                            btn.dataset.bound = '1';
                            attachRemove(btn);
                        }
                    });
                });

                container.addEventListener('click', function(e){
                    if (e.target && e.target.classList.contains('comparative-remove')){
                        const row = e.target.closest('div.flex') || e.target.closest('div');
                        if (row) row.remove();
                    }
                });
            })();
        </script>
</x-app-layout>
