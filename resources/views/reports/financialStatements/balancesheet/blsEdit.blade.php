<x-app-layout>
    <div class="flex-1 flex flex-col">
        <main class="flex-1 p-6">
                <!-- Header -->
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 id="pageHeading" class="text-2xl font-semibold text-gray-800">Statement of Financial Position</h1>
                        <p class="text-sm text-gray-500">Edit Statement of Financial Position</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-arrow-left"></i>
                            <span class="text-sm">Back</span>
                        </a>
                    </div>
                </div>

                <!-- Card / Form: single-column like screenshot -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <form action="{{ route('reports.financial.balance-sheet.store') }}" method="POST" class="p-6 space-y-6 max-w-3xl">
                        @csrf

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input id="title" type="text" name="title" value="{{ old('title', $report->title ?? 'Statement of Financial Position') }}" class="mt-1 w-full max-w-sm border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <input id="description" type="text" name="description" placeholder="Optional" value="{{ old('description') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date + Column name row -->
                        <div>
                            <div id="columnsContainer" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl">
                                <div class="sm:col-span-5">
                                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                                    <div class="relative mt-1">
                                        <input id="date" type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                        <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                                    </div>
                                    @error('date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-6">
                                    <label for="column_name" class="block text-sm font-medium text-gray-700">Column label</label>
                                    <input id="column_name" type="text" name="column_name" placeholder="Optional" value="{{ old('column_name') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                                </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button id="addComparativeBtn" type="button" class="inline-flex items-center gap-2 px-3 py-2 border rounded-md text-gray-700 hover:bg-gray-50">
                                    Add comparative column
                                    <i class="fas fa-caret-down"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Accounting method -->
                        <div class="max-w-xs">
                            <label for="accounting_method" class="block text-sm font-medium text-gray-700">Accounting method</label>
                            <select id="accounting_method" name="accounting_method" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="accrual" {{ old('accounting_method', 'accrual') === 'accrual' ? 'selected' : '' }}>Accrual basis</option>
                                <option value="cash" {{ old('accounting_method') === 'cash' ? 'selected' : '' }}>Cash basis</option>
                            </select>
                            @error('accounting_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rounding -->
                        <div class="max-w-[7rem]">
                            <label for="rounding" class="block text-sm font-medium text-gray-700">Rounding</label>
                            <select id="rounding" name="rounding" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="off" selected>Off</option>
                            </select>
                        </div>

                        <!-- Layout -->
                        <div class="max-w-sm">
                            <label for="layout" class="block text-sm font-medium text-gray-700">Layout</label>
                            @php $layoutVal = old('layout', 'assets-minus-liabilities-equals-equity'); @endphp
                            <select id="layout" name="layout" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
                                <option value="assets-minus-liabilities-equals-equity" {{ $layoutVal === 'assets-minus-liabilities-equals-equity' ? 'selected' : '' }}>Assets - Liabilities = Equity</option>
                                <option value="assets-equals-liabilities-plus-equity" {{ $layoutVal === 'assets-equals-liabilities-plus-equity' ? 'selected' : '' }}>Assets = Liabilities + Equity</option>
                                <option value="assets-equals-equity-plus-liabilities" {{ $layoutVal === 'assets-equals-equity-plus-liabilities' ? 'selected' : '' }}>Assets = Equity + Liabilities</option>
                            </select>
                        </div>

                        <!-- Footer -->
                        <div class="max-w-3xl">
                            <label for="footer" class="block text-sm font-medium text-gray-700">Footer</label>
                            <textarea id="footer" name="footer" rows="6" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder=""></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-gray-100 flex justify-start">
                            <button id="submitBtn" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm">Save Changes</button>
                        </div>
                    </form>
                </div>
        </main>
    </div>

    <script>
    (function(){
        const columnsContainer = document.getElementById('columnsContainer');
        const addComparativeBtn = document.getElementById('addComparativeBtn');
        const baseDate = document.getElementById('date');
        const titleInput = document.getElementById('title');
        const submitBtn = document.getElementById('submitBtn');
        const pageHeading = document.getElementById('pageHeading');

        let compIndex = 0;

        function buildComparativeRow(index){
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl';
            row.innerHTML = `
                <div class="sm:col-span-5">
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <div class="relative mt-1">
                        <input type="date" name="comparatives[${index}][date]" class="w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                        <i class="far fa-calendar-alt absolute right-3 top-3.5 text-gray-400"></i>
                    </div>
                </div>
                <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Column name</label>
                    <input type="text" name="comparatives[${index}][column_name]" placeholder="Optional" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" />
                </div>
                <div class="sm:col-span-1 flex items-end">
                    <button type="button" class="remove-comp mt-1 inline-flex items-center justify-center h-10 w-10 border rounded-md text-gray-600 hover:bg-gray-50" title="Remove column">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>`;
            row.querySelector('.remove-comp').addEventListener('click', () => row.remove());
            return row;
        }

        addComparativeBtn?.addEventListener('click', () => {
            const row = buildComparativeRow(compIndex++);
            columnsContainer.appendChild(row);
        });

        function validate(){
            const ok = Boolean((titleInput?.value || '').trim()) && Boolean(baseDate?.value);
            if(submitBtn){
                submitBtn.disabled = !ok;
                submitBtn.classList.toggle('opacity-50', !ok);
                submitBtn.classList.toggle('cursor-not-allowed', !ok);
            }
            return ok;
        }
        titleInput?.addEventListener('input', () => {
            const t = (titleInput.value || '').trim();
            if(pageHeading) pageHeading.textContent = t || 'Statement of Financial Position';
            document.title = t || 'Statement of Financial Position';
            validate();
        });
        baseDate?.addEventListener('change', () => {
            const d = baseDate.value;
            if(d){
                const dt = new Date(d);
                const nice = dt instanceof Date && !isNaN(dt) ? dt.toLocaleDateString(undefined, { day:'2-digit', month:'short', year:'numeric' }) : d;
                const suggested = `Statement of Financial Position as at ${nice}`;
                const current = (titleInput.value || '').trim();
                if(!current || current === 'Statement of Financial Position'){
                    titleInput.value = suggested;
                    if(pageHeading) pageHeading.textContent = suggested;
                    document.title = suggested;
                }
            }
            validate();
        });
        (function init(){
            const t = (titleInput?.value || '').trim() || 'Statement of Financial Position';
            if(pageHeading) pageHeading.textContent = t;
            document.title = t;
            validate();
        })();
    })();
    </script>
</x-app-layout>