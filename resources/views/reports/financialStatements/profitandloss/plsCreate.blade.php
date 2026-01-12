<x-app-layout>
<main class="p-4 md:p-5 lg:p-6">

    <!-- Modern Minimal Breadcrumbs -->
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center text-sm text-gray-500 gap-2">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 transition">
                    Dashboard
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li>
                <a href="{{ route('reports.financialStatements.profit-and-loss.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                    P&L Reports
                </a>
            </li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-900 font-semibold">Create New</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Profit and Loss Report</h1>
            <p class="text-gray-600 mt-1">Set up a new P&L statement to track income, expenses, and profitability</p>
        </div>

        <a href="{{ route('reports.financialStatements.profit-and-loss.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 hover:bg-gray-100 transition">
            <span class="text-lg -mr-1">←</span>
            <span>Back to List</span>
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">

        <form action="{{ route('reports.financial.profit-and-loss.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT COLUMN -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- REPORT DETAILS -->
                    <section>
                        <header class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                            <div class="w-8 h-8 bg-blue-50 text-blue-700 rounded-md flex items-center justify-center font-bold">
                                R
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Report Details</h2>
                                <p class="text-sm text-gray-600">Basic information about your report</p>
                            </div>
                        </header>

                        <div class="space-y-5">

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Title <span class="text-red-500">*</span></label>
                                <input type="text"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', 'Profit and Loss Statement') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="e.g., Q3 2025 Profit & Loss Statement"
                                       required>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Description</label>
                                <input type="text"
                                       name="description"
                                       value="{{ old('description') }}"
                                       placeholder="Optional context for readers"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Helps others understand the purpose of this report</p>
                            </div>

                        </div>
                    </section>

                    <!-- DATE RANGE -->
                    <section>
                        <header class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                            <div class="w-8 h-8 bg-emerald-50 text-emerald-700 rounded-md flex items-center justify-center font-bold">
                                D
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Date Range</h2>
                                <p class="text-sm text-gray-600">Choose the reporting period</p>
                            </div>
                        </header>

                        <div class="space-y-5">

                            <!-- Quick Presets -->
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-2">Quick Presets</label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                            data-range="this-month"
                                            class="date-preset px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                        This Month
                                    </button>
                                    <button type="button"
                                            data-range="last-month"
                                            class="date-preset px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                        Last Month
                                    </button>
                                    <button type="button"
                                            data-range="ytd"
                                            class="date-preset px-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                                        Year to Date
                                    </button>
                                </div>
                            </div>

                            <!-- Date Inputs (Modern Version with Flatpickr) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                                <!-- Start Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-800 mb-2">
                                        Start Date <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        id="from"
                                        name="from"
                                        placeholder="Select start date"
                                        value="{{ old('from', date('Y-m-d')) }}"
                                        required
                                        class="cursor-pointer w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>

                                <!-- End Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-800 mb-2">
                                        End Date <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        id="to"
                                        name="to"
                                        placeholder="Select end date"
                                        value="{{ old('to', date('Y-m-d')) }}"
                                        required
                                        class="cursor-pointer w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />

                                    <p id="clientDateError" class="text-sm text-red-600 mt-1 hidden">
                                        Start date cannot be after end date
                                    </p>
                                </div>

                            </div>

                        </div>
                    </section>

                    <!-- SETTINGS -->
                    <section>
                        <header class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                            <div class="w-8 h-8 bg-purple-50 text-purple-700 rounded-md flex items-center justify-center font-bold">
                                S
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Report Settings</h2>
                                <p class="text-sm text-gray-600">Choose calculation method</p>
                            </div>
                        </header>

                        <div class="space-y-6">
                            @php $method = old('accounting_method', 'accrual'); @endphp

                            <!-- Accounting Method -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition
                                            {{ $method === 'accrual' ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-gray-400' }}">
                                    <input type="radio" name="accounting_method" value="accrual"
                                           class="mt-1 text-blue-600"
                                           {{ $method === 'accrual' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-medium text-gray-900">Accrual</p>
                                        <p class="text-xs text-gray-600">Record revenue & expenses when incurred</p>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition
                                            {{ $method === 'cash' ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-gray-400' }}">
                                    <input type="radio" name="accounting_method" value="cash"
                                           class="mt-1 text-green-600"
                                           {{ $method === 'cash' ? 'checked' : '' }}>
                                    <div>
                                        <p class="font-medium text-gray-900">Cash</p>
                                        <p class="text-xs text-gray-600">Record only when cash is received or paid</p>
                                    </div>
                                </label>

                            </div>

                            <!-- Rounding -->
                            <div>
                                <label class="block text-sm font-medium text-gray-800 mb-1">Rounding</label>
                                @php $r = old('rounding', 'off'); @endphp
                                <select name="rounding"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="off" {{ $r == 'off' ? 'selected' : '' }}>No Rounding</option>
                                    <option value="nearest" {{ $r == 'nearest' ? 'selected' : '' }}>Nearest</option>
                                    <option value="1" {{ $r == '1' ? 'selected' : '' }}>Nearest 1</option>
                                    <option value="10" {{ $r == '10' ? 'selected' : '' }}>Nearest 10</option>
                                    <option value="100" {{ $r == '100' ? 'selected' : '' }}>Nearest 100</option>
                                </select>
                            </div>

                        </div>
                    </section>

                    <!-- FOOTER NOTES -->
                    <section>
                        <header class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-200">
                            <div class="w-8 h-8 bg-amber-50 text-amber-700 rounded-md flex items-center justify-center font-bold">
                                N
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Additional Notes</h2>
                                <p class="text-sm text-gray-600">Optional text shown at the bottom of report</p>
                            </div>
                        </header>

                        <!-- Footer -->
                        <textarea id="footerTextarea"
                                  name="footer"
                                  rows="4"
                                  maxlength="500"
                                  placeholder="Optional notes or disclaimers..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('footer') }}</textarea>

                        <p class="text-xs text-gray-500 mt-1">
                            <span id="footerCount">0</span>/500 characters
                        </p>
                    </section>

                </div>

                <!-- RIGHT COLUMN (LIVE PREVIEW) -->
                <aside class="lg:col-span-1">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 sticky top-20">

                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Live Preview</h3>

                        <div class="space-y-4">

                            <!-- Preview Title -->
                            <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase">Title</p>
                                <p id="previewTitle" class="font-semibold text-gray-900 mt-1">
                                    {{ old('title', 'Profit and Loss Statement') }}
                                </p>
                            </div>

                            <!-- Preview Date Range -->
                            <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase">Period</p>
                                <p id="previewRange" class="text-gray-900 mt-1">
                                    {{ old('from') }} → {{ old('to') }}
                                </p>
                            </div>

                            <!-- Preview Settings -->
                            <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-200 space-y-2">
                                <p class="text-xs text-gray-500 uppercase">Settings</p>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Method:</span>
                                    <span id="previewMethod" class="text-gray-900 font-medium">{{ ucfirst($method) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Rounding:</span>
                                    <span id="previewRounding" class="text-gray-900 font-medium">{{ $r }}</span>
                                </div>
                            </div>

                            <!-- Preview Footer -->
                            <div class="p-3 bg-white rounded-lg shadow-sm border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase">Footer</p>
                                <p id="previewFooter" class="text-sm text-gray-800 mt-1 break-words">
                                    {{ old('footer') ?: 'No footer text added' }}
                                </p>
                            </div>

                        </div>

                    </div>
                </aside>

            </div>

            <!-- ACTION BUTTONS -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">

                <p class="text-sm text-gray-600">Fields with * are required</p>

                <div class="flex gap-3">
                    <a href="{{ route('reports.financialStatements.profit-and-loss.index') }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-100 transition">Cancel</a>

                    <button type="submit"
                            id="submitButton"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        Create Report
                    </button>
                </div>

            </div>

        </form>

    </div>


    <!-- Scripts for Live Preview + Date Validation -->
    <script>
        const titleInput = document.getElementById('title');
        const fromInput = document.getElementById('from');
        const toInput = document.getElementById('to');
        const footer = document.getElementById('footerTextarea');
        const previewTitle = document.getElementById('previewTitle');
        const previewRange = document.getElementById('previewRange');
        const previewFooter = document.getElementById('previewFooter');
        const footerCount = document.getElementById('footerCount');

        // Update preview title
        titleInput?.addEventListener('input', () => {
            previewTitle.textContent = titleInput.value || "Untitled Report";
        });

        // Update preview date range
        function updateDateRange() {
            previewRange.textContent = `${fromInput.value} → ${toInput.value}`;
        }
        fromInput?.addEventListener('change', updateDateRange);
        toInput?.addEventListener('change', updateDateRange);

        // Footer character counter + preview
        footer?.addEventListener('input', () => {
            footerCount.textContent = footer.value.length;
            previewFooter.textContent = footer.value || "No footer text added";
        });

        // Date validation
        const clientDateError = document.getElementById('clientDateError');
        const submitBtn = document.getElementById('submitButton');

        function validateDates() {
            if (fromInput.value > toInput.value) {
                clientDateError.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                clientDateError.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        fromInput?.addEventListener('change', validateDates);
        toInput?.addEventListener('change', validateDates);
        validateDates();


        document.addEventListener("DOMContentLoaded", function () {

            // Start Date Picker
            flatpickr("#from", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                defaultDate: "{{ old('from', date('Y-m-d')) }}"
            });

            // End Date Picker
            flatpickr("#to", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                defaultDate: "{{ old('to', date('Y-m-d')) }}"
            });

        });
    </script>

</main>
</x-app-layout>
