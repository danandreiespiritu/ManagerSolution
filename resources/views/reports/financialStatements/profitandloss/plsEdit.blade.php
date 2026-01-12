<x-app-layout>
<div class="flex flex-col flex-1">
    <main class="flex-1 p-6">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Profit & Loss Report</h1>
                <p class="text-gray-500 text-sm">Modify report details, dates, and settings.</p>
            </div>

            <a href="{{ url()->previous() }}"
                class="flex items-center gap-2 px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <!-- Form Container -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form method="POST" action="{{ route('reports.financial.profit-and-loss.update', $report->id) }}" class="p-6 space-y-10">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- LEFT SIDE FORM -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- TITLE -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-800">Title</label>
                            <input type="text" id="title" name="title"
                                value="{{ old('title', $report->title) }}"
                                placeholder="e.g., Q1 2025 Profit & Loss"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-gray-800">
                                Description <span class="text-gray-500 text-xs">(optional)</span>
                            </label>
                            <input type="text" id="description" name="description"
                                value="{{ old('description', $report->description) }}"
                                placeholder="Brief description of the report"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <!-- DATE RANGE -->
                        <div class="space-y-4">

                            <label class="text-sm font-medium text-gray-800">Date Range</label>

                            <div class="flex gap-2 flex-wrap">
                                <button type="button" class="date-preset modern-preset" data-range="this-month">This Month</button>
                                <button type="button" class="date-preset modern-preset" data-range="last-month">Last Month</button>
                                <button type="button" class="date-preset modern-preset" data-range="ytd">YTD</button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="text-sm font-medium text-gray-800">Start Date</label>
                                    <input type="text" id="from" name="from"
                                        value="{{ old('from', optional($report->date_from)->format('Y-m-d')) }}"
                                        placeholder="Select start date"
                                        class="modern-date border border-gray-400 rounded-lg text-gray-900 cursor-pointer py- px-2" />
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-gray-800">End Date</label>
                                    <input type="text" id="to" name="to"
                                        value="{{ old('to', optional($report->date_to)->format('Y-m-d')) }}"
                                        placeholder="Select end date"
                                        class="modern-date border border-gray-400 rounded-lg text-gray-900 cursor-pointer py-1 px-2" />

                                    <p id="clientDateError" class="text-sm text-red-600 hidden mt-1">
                                        Start date cannot be after end date.
                                    </p>
                                </div>
                            </div>
                        </div>

                   <!-- ACCOUNTING METHOD -->
                    <!-- ACCOUNTING METHOD -->
                    <div>
                        <label class="text-sm font-medium text-gray-900">Accounting Method</label>

                        <div class="grid grid-cols-2 gap-3 mt-2">
                            @php $am = old('accounting_method', $report->accounting_method); @endphp

                            <label class="flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 
                                        bg-white cursor-pointer text-gray-900 hover:bg-gray-100
                                        {{ $am=='accrual' ? 'border-blue-500 bg-blue-50 text-blue-700' : '' }}">
                                <input type="radio" name="accounting_method" value="accrual" class="hidden"
                                    {{ $am=='accrual' ? 'checked' : '' }}>
                                Accrual
                            </label>

                            <label class="flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 
                                        bg-white cursor-pointer text-gray-900 hover:bg-gray-100
                                        {{ $am=='cash' ? 'border-blue-500 bg-blue-50 text-blue-700' : '' }}">
                                <input type="radio" name="accounting_method" value="cash" class="hidden"
                                    {{ $am=='cash' ? 'checked' : '' }}>
                                Cash
                            </label>
                        </div>
                    </div>

                    <!-- ROUNDING -->
                    <div>
                        <label class="text-sm font-medium text-gray-900">Number Rounding</label>
                        <select id="rounding" name="rounding"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900
                                    shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @php $r = old('rounding', $report->rounding ?? 'off'); @endphp
                            <option value="off" {{ $r=='off' ? 'selected' : '' }}>None</option>
                            <option value="nearest" {{ $r=='nearest' ? 'selected' : '' }}>Nearest</option>
                            <option value="1" {{ $r=='1' ? 'selected' : '' }}>Nearest 1</option>
                            <option value="10" {{ $r=='10' ? 'selected' : '' }}>Nearest 10</option>
                            <option value="100" {{ $r=='100' ? 'selected' : '' }}>Nearest 100</option>
                        </select>
                    </div>

                    <!-- FOOTER NOTES -->
                    <div>
                        <label class="text-sm font-medium text-gray-900">Footer Notes</label>
                        <textarea id="footerTextarea"
                                name="footer"
                                rows="4"
                                maxlength="500"
                                placeholder="Optional footer notes..."
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900
                                        shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none">{{ old('footer', $report->footer) }}</textarea>

                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Max 500 characters</span>
                            <span><span id="footerCount">0</span>/500</span>
                        </div>
                    </div>




                    </div>

                    <!-- RIGHT SIDE PREVIEW -->
                <aside class="lg:col-span-1">
                    <div class="sticky top-6 space-y-4 bg-white border border-gray-200 rounded-xl p-5 shadow-sm">

                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Live Preview</h3>
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">Live</span>
                        </div>

                        <div class="space-y-4 text-sm">

                            <!-- Title -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Title</p>
                                <p id="previewTitle" class="mt-1 font-semibold text-gray-900 break-words">
                                    {{ old('title', $report->title) ?: 'Untitled report' }}
                                </p>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Date Range</p>
                                <p id="previewRange" class="mt-1 text-gray-900">
                                    {{ old('from', optional($report->date_from)->format('Y-m-d')) }} 
                                    <span class="mx-1 text-gray-400">→</span>
                                    {{ old('to', optional($report->date_to)->format('Y-m-d')) }}
                                </p>
                            </div>

                            <!-- Settings Badges -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Settings</p>
                                <div class="flex flex-wrap gap-2 mt-1">

                                    <span id="previewMethod"
                                        class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-900 text-xs font-medium border border-gray-200">
                                        {{ $am=='cash' ? 'Cash Basis' : 'Accrual Basis' }}
                                    </span>

                                    <span id="previewRounding"
                                        class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-900 text-xs font-medium border border-gray-200">
                                        {{ $r=='off'?'No Rounding':($r=='nearest'?'Nearest':"Nearest $r") }}
                                    </span>

                                </div>
                            </div>

                            <!-- Footer -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Footer Notes</p>
                                <p id="previewFooter" class="mt-1 text-gray-900 break-words max-h-20 overflow-y-auto">
                                    {{ old('footer', $report->footer) ?: '—' }}
                                </p>
                            </div>

                        </div>
                    </div>
                </aside>


                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex justify-end gap-3 pt-6 border-t">
                    <a href="{{ route('reports.financial.profit-and-loss.show', $report->id) }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>

<!-- Modern Styles -->
<style>
.modern-date {
    @apply w-full px-3 py-2 cursor-pointer bg-white border border-gray-300 rounded-lg text-gray-900 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
}

.modern-preset {
    @apply px-3 py-1.5 text-xs border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-100;
}

.modern-select {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500;
}

.modern-option {
    @apply flex items-center justify-center px-3 py-2 rounded-lg border border-gray-300 text-gray-800 cursor-pointer select-none transition;
}

.modern-option:hover {
    @apply bg-gray-100;
}

.modern-selected {
    @apply border-blue-400 bg-blue-50 text-blue-700;
}

.modern-textarea {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500;
}

.modern-badge {
    @apply inline-flex items-center px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-xs;
}
</style>

<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    flatpickr("#from", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        allowInput: true
    });

    flatpickr("#to", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        allowInput: true
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const options = document.querySelectorAll('[name="accounting_method"]');

    options.forEach(opt => {
        opt.addEventListener('change', function () {
            // remove active classes from all labels
            document.querySelectorAll('.accounting-option').forEach(l => {
                l.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
                l.classList.add('border-gray-300', 'bg-white', 'text-gray-900');
            });

            // apply to selected label
            const label = this.closest('label');
            label.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
        });
    });
});
</script>
</x-app-layout>
