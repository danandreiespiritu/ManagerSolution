<x-app-layout>
<main class="p-4 md:p-6 lg:p-8 bg-gray-50">

    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center text-sm text-gray-500 gap-2">
            <li>
                <a class="hover:text-blue-600" href="{{ route('dashboard') }}">Dashboard</a>
            </li>
            <li>/</li>
            <li>
                <a class="hover:text-blue-600" href="{{ route('reports.financialStatements.profit-and-loss.index') }}">P&L Reports</a>
            </li>
            <li>/</li>
            <li class="text-gray-700 font-medium">Create New</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Create Profit & Loss Report</h1>
            <p class="text-gray-600 text-sm mt-1">Set the details, date range, and preferences for your P&L report.</p>
        </div>

        <a href="{{ route('reports.financialStatements.profit-and-loss.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-gray-700 hover:bg-gray-100 transition">
            ← Back to List
        </a>
    </div>

    <!-- Main Container -->
    <form action="{{ route('reports.financial.profit-and-loss.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT PANEL -->
            <div class="lg:col-span-2 space-y-6">

                <!-- SECTION CARD -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-medium text-gray-800 mb-3">Report Details</h2>

                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   value="{{ old('title', 'Profit and Loss Statement') }}"
                                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-50 focus:border-blue-500 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="text-sm font-medium text-gray-700">Description</label>
                            <input type="text"
                                   name="description"
                                   value="{{ old('description') }}"
                                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-50 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Optional summary or comments">
                        </div>
                    </div>
                </div>

                <!-- DATE RANGE -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="text-sm font-medium text-gray-700">Start Date *</label>
                            <input type="date"
                                   id="from"
                                   name="from"
                                   value="{{ old('from', date('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">End Date *</label>
                            <input type="date"
                                   id="to"
                                   name="to"
                                   value="{{ old('to', date('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            <p id="clientDateError" class="text-xs text-red-500 mt-1 hidden">
                                Start date cannot be after end date.
                            </p>
                        </div>

                    </div>
                </div>

                <!-- SETTINGS -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-medium text-gray-800 mb-3">Settings</h2>

                    @php $method = old('accounting_method', 'accrual'); @endphp
                    @php $r = old('rounding', 'off'); @endphp

                    <!-- Accounting method -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                        <label class="flex gap-3 p-3 border rounded-lg cursor-pointer transition
                                  {{ $method == 'accrual' ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-gray-400' }}">
                            <input type="radio" name="accounting_method" value="accrual" {{ $method == 'accrual' ? 'checked' : '' }}>
                            <div>
                                <p class="font-medium text-gray-900">Accrual</p>
                                <p class="text-xs text-gray-600">Recognizes revenue when earned</p>
                            </div>
                        </label>

                        <label class="flex gap-3 p-3 border rounded-lg cursor-pointer transition
                                  {{ $method == 'cash' ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-gray-400' }}">
                            <input type="radio" name="accounting_method" value="cash" {{ $method == 'cash' ? 'checked' : '' }}>
                            <div>
                                <p class="font-medium text-gray-900">Cash Basis</p>
                                <p class="text-xs text-gray-600">Records only when cash moves</p>
                            </div>
                        </label>

                    </div>

                    <!-- Rounding -->
                    <div>
                        <label class="text-sm font-medium text-gray-700">Rounding</label>
                        <select name="rounding"
                                class="mt-1 w-full rounded-lg px-3 py-2 border border-gray-300 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <option value="off" {{ $r=='off' ? 'selected' : '' }}>No rounding</option>
                            <option value="nearest" {{ $r=='nearest' ? 'selected' : '' }}>Nearest</option>
                            <option value="1" {{ $r=='1' ? 'selected' : '' }}>Nearest 1</option>
                            <option value="10" {{ $r=='10' ? 'selected' : '' }}>Nearest 10</option>
                            <option value="100" {{ $r=='100' ? 'selected' : '' }}>Nearest 100</option>
                        </select>
                    </div>
                </div>

                <!-- NOTES -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-medium text-gray-800 mb-3">Notes (Optional)</h2>
                    <textarea name="footer"
                              id="footerTextarea"
                              rows="4"
                              maxlength="500"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Add any notes or disclaimers here...">{{ old('footer') }}</textarea>

                    <p class="text-xs text-gray-500 mt-1">
                        <span id="footerCount">0</span>/500 characters
                    </p>
                </div>

            </div>

            <!-- RIGHT PANEL: LIVE PREVIEW -->
            <div class="lg:col-span-1">
                <div class="sticky top-20 bg-white border border-gray-200 rounded-xl shadow-sm p-4 space-y-4">

                    <h3 class="text-lg font-medium text-gray-800">Live Preview</h3>

                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-xs text-gray-500 uppercase">Title</p>
                        <p id="previewTitle" class="font-semibold text-gray-900 mt-1">
                            {{ old('title', 'Profit and Loss Statement') }}
                        </p>
                    </div>

                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-xs text-gray-500 uppercase">Period</p>
                        <p id="previewRange" class="text-gray-800 mt-1">
                            {{ old('from') }} → {{ old('to') }}
                        </p>
                    </div>

                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-xs text-gray-500 uppercase">Method</p>
                        <p id="previewMethod" class="text-gray-800 mt-1">{{ ucfirst($method) }}</p>

                        <p class="text-xs text-gray-500 uppercase mt-3">Rounding</p>
                        <p id="previewRounding" class="text-gray-800 mt-1">{{ $r }}</p>
                    </div>

                    <div class="p-3 rounded-lg border bg-gray-50">
                        <p class="text-xs text-gray-500 uppercase">Footer</p>
                        <p id="previewFooter" class="text-gray-700 mt-1">
                            {{ old('footer') ?: 'No footer text added' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="mt-8 flex justify-between items-center border-t pt-6">
            <p class="text-sm text-gray-600">Fields marked with * are required</p>

            <div class="flex gap-3">
                <a href="{{ route('reports.financialStatements.profit-and-loss.index') }}"
                   class="px-5 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </a>

                <button type="submit"
                        id="submitButton"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                    Create Report
                </button>
            </div>
        </div>

    </form>

    <!-- JS Enhancements -->
    <script>
        const titleInput = document.getElementById('title');
        const fromInput = document.getElementById('from');
        const toInput = document.getElementById('to');
        const footerInput = document.getElementById('footerTextarea');

        const previewTitle = document.getElementById('previewTitle');
        const previewRange = document.getElementById('previewRange');
        const previewFooter = document.getElementById('previewFooter');
        const footerCount = document.getElementById('footerCount');

        // Update preview title
        titleInput.addEventListener('input', () => {
            previewTitle.textContent = titleInput.value || "Untitled Report";
        });

        // Update date range preview
        function updateDateRange() {
            previewRange.textContent = `${fromInput.value} → ${toInput.value}`;
        }
        fromInput.addEventListener('change', updateDateRange);
        toInput.addEventListener('change', updateDateRange);

        // Footer counter
        footerInput.addEventListener('input', () => {
            footerCount.textContent = footerInput.value.length;
            previewFooter.textContent = footerInput.value || "No footer text added";
        });

        // Date validation
        const dateError = document.getElementById('clientDateError');
        const submitBtn = document.getElementById('submitButton');

        function validateDates() {
            if (fromInput.value > toInput.value) {
                dateError.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50');
            } else {
                dateError.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
            }
        }
        fromInput.addEventListener('change', validateDates);
        toInput.addEventListener('change', validateDates);

        // Flatpickr initialization
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#from", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                defaultDate: "{{ old('from', date('Y-m-d')) }}"
            });

            flatpickr("#to", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "F j, Y",
                defaultDate: "{{ old('to', date('Y-m-d')) }}"
            });
        });
    </script>

</main>
</x-app-layout>
