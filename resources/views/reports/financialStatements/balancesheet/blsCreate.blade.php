<x-app-layout>

<div class="flex min-h-screen bg-gray-50 text-gray-900">
    <main class="flex-1 p-6">

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Balance Sheet</h1>
                <p class="text-sm text-gray-600">Create a new balance sheet report</p>
            </div>

            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>


        <!-- FORM CARD -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 max-w-3xl mx-auto space-y-7">

            <form action="{{ route('reports.financial.balance-sheet.store') }}" method="POST" class="space-y-7">
                @csrf

                <!-- TITLE -->
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-900">Title</label>
                    <input type="text" name="title"
                           value="{{ old('title', 'Balance Sheet') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white shadow-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <!-- DESCRIPTION -->
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-900">Description</label>
                    <input type="text" name="description" placeholder="Optional"
                           value="{{ old('description') }}"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white shadow-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>


                <!-- DATE RANGE & COLUMN NAME -->
                <div class="space-y-3">
                    <label class="text-sm font-semibold text-gray-900">Main Column</label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs text-gray-700">From</label>
                                <input type="text"
                                       id="from"
                                       name="from"
                                       placeholder="Start date"
                                       value="{{ old('from', date('Y-m-d')) }}"
                                       class="cursor-pointer w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm text-gray-900"
                                       autocomplete="off" />
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs text-gray-700">To</label>
                                <input type="text"
                                       id="to"
                                       name="to"
                                       placeholder="End date"
                                       value="{{ old('to', date('Y-m-d')) }}"
                                       class="cursor-pointer w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm text-gray-900"
                                       autocomplete="off" />
                            </div>
                        </div>

                        <!-- Column name -->
                        <div class="space-y-1">
                            <label class="text-xs text-gray-700">Column name</label>
                            <input type="text" name="column_name" placeholder="Optional"
                                   value="{{ old('column_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm text-gray-900" />
                        </div>

                    </div>
                </div>


                <!-- COMPARATIVE COLUMNS -->
                <div class="space-y-3">

                    <label class="text-sm font-semibold text-gray-900">Comparative Columns</label>

                    <div id="columnsContainer" class="space-y-3"></div>

                    <button type="button" id="addComparativeBtn"
                            class="mt-2 inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 hover:bg-gray-100 transition">
                        Add comparative column
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </div>


                <!-- ACCOUNTING METHOD -->
                <div class="space-y-1 max-w-xs">
                    <label class="text-sm font-semibold text-gray-900">Accounting method</label>
                    <select name="accounting_method"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white shadow-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="accrual" {{ old('accounting_method', 'accrual') === 'accrual' ? 'selected' : '' }}>Accrual basis</option>
                        <option value="cash" {{ old('accounting_method') === 'cash' ? 'selected' : '' }}>Cash basis</option>
                    </select>
                </div>


                <!-- ACCOUNTING EQUATION -->
                <div class="space-y-1 max-w-sm">
                    <label class="text-sm font-semibold text-gray-900">Accounting equation</label>
                    <select name="equation"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white shadow-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="standard" {{ old('equation') === 'standard' ? 'selected' : '' }}>ASSET = LIABILITIES + EQUITY</option>
                        <option value="extended" {{ old('equation') === 'extended' ? 'selected' : '' }}>ASSET = LIABILITIES + EQUITY + REVENUE - EXPENSES</option>
                    </select>
                </div>


                <!-- FOOTER NOTES -->
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-900">Footer Notes</label>
                    <textarea name="footer" rows="5"
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white shadow-sm text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Optional notes..."></textarea>
                </div>


                <!-- SUBMIT -->
                <div class="pt-4 border-t border-gray-200">
                    <button id="submitBtn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition">
                        Create Report
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>


<!-- JS -->
<script>
flatpickr("#from", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    defaultDate: "{{ old('from', date('Y-m-d')) }}"
});

flatpickr("#to", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    defaultDate: "{{ old('to', date('Y-m-d')) }}"
});

(function () {

    const container = document.getElementById('columnsContainer');
    const addBtn = document.getElementById('addComparativeBtn');

    let index = 0;

    function createRow(i) {
        const row = document.createElement('div');
        row.className = "grid grid-cols-1 sm:grid-cols-12 gap-4 items-end";

        row.innerHTML = `
            <div class="sm:col-span-5 space-y-1">
                <label class="text-xs text-gray-700">Date</label>
                <input type="text"
                       name="comparatives[${i}][date]"
                       placeholder="Select date"
                       class="comp-date cursor-pointer w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm text-gray-900"
                       autocomplete="off" />
            </div>

            <div class="sm:col-span-6 space-y-1">
                <label class="text-xs text-gray-700">Column name</label>
                <input type="text" name="comparatives[${i}][column_name]"
                       class="w-full px-3 py-2 border border-gray-300 bg-white rounded-lg shadow-sm text-gray-900"
                       placeholder="Optional" />
            </div>

            <div class="sm:col-span-1 flex items-end">
                <button type="button"
                        class="remove-comp flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition">
                    <i class="far fa-trash-alt"></i>
                </button>
            </div>
        `;

        // Remove event
        row.querySelector('.remove-comp').onclick = () => row.remove();

        container.appendChild(row);

        // activate flatpickr on new date input
        flatpickr(row.querySelector('.comp-date'), {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true
        });
    }

    addBtn.addEventListener('click', () => {
        createRow(index++);
    });

})();
</script>

</x-app-layout>
