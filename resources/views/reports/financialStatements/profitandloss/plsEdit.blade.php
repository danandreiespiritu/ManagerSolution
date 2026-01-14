<x-app-layout>
<div class="flex flex-col flex-1 bg-gray-50">
    <main class="flex-1 p-6">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Profit & Loss Report</h1>
                <p class="text-gray-600 text-sm">Update title, period, method, and notes.</p>
            </div>

            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition">
                ← Back
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <form method="POST" action="{{ route('reports.financial.profit-and-loss.update', $report->id) }}" class="p-6 space-y-10">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- LEFT CONTENT -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- Title -->
                        <section class="space-y-2">
                            <label class="text-sm font-medium text-gray-800">Title</label>
                            <input type="text" id="title" name="title"
                                value="{{ old('title', $report->title) }}"
                                placeholder="e.g., Q1 2025 Profit & Loss"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </section>

                        <!-- Description -->
                        <section class="space-y-2">
                            <label class="text-sm font-medium text-gray-800">Description <span class="text-xs text-gray-500">(optional)</span></label>
                            <input type="text" id="description" name="description"
                                value="{{ old('description', $report->description) }}"
                                placeholder="Brief description of the report"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </section>

                        <!-- Date Range -->
                        <section class="space-y-3">

                            <!-- Date Inputs -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="text-sm text-gray-700">Start Date</label>
                                    <input type="text" id="from" name="from"
                                        value="{{ old('from', optional($report->date_from)->format('Y-m-d')) }}"
                                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="text-sm text-gray-700">End Date</label>
                                    <input type="text" id="to" name="to"
                                        value="{{ old('to', optional($report->date_to)->format('Y-m-d')) }}"
                                        class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

                                    <p id="clientDateError" class="text-xs text-red-600 mt-1 hidden">
                                        Start date cannot be after end date.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Accounting Method -->
                        <section>
                            <label class="text-sm font-medium text-gray-800">Accounting Method</label>

                            @php $am = old('accounting_method', $report->accounting_method); @endphp

                            <div class="grid grid-cols-2 gap-3 mt-2">
                                <label class="flex items-center justify-center px-4 py-2 border rounded-lg cursor-pointer transition 
                                             {{ $am=='accrual' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-900' }}">
                                    <input type="radio" name="accounting_method" value="accrual" class="hidden" {{ $am=='accrual' ? 'checked' : '' }}>
                                    Accrual
                                </label>

                                <label class="flex items-center justify-center px-4 py-2 border rounded-lg cursor-pointer transition
                                             {{ $am=='cash' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-900' }}">
                                    <input type="radio" name="accounting_method" value="cash" class="hidden" {{ $am=='cash' ? 'checked' : '' }}>
                                    Cash
                                </label>
                            </div>
                        </section>

                        <!-- Rounding -->
                        <section>
                            <label class="text-sm font-medium text-gray-800">Number Rounding</label>
                            @php $r = old('rounding', $report->rounding ?? 'off'); @endphp

                            <select id="rounding" name="rounding"
                                class="w-full mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="off" {{ $r=='off' ? 'selected' : '' }}>None</option>
                                <option value="nearest" {{ $r=='nearest' ? 'selected' : '' }}>Nearest</option>
                                <option value="1" {{ $r=='1' ? 'selected' : '' }}>Nearest 1</option>
                                <option value="10" {{ $r=='10' ? 'selected' : '' }}>Nearest 10</option>
                                <option value="100" {{ $r=='100' ? 'selected' : '' }}>Nearest 100</option>
                            </select>
                        </section>

                        <!-- Footer Notes -->
                        <section>
                            <label class="text-sm font-medium text-gray-800">Footer Notes</label>
                            <textarea id="footerTextarea"
                                      name="footer"
                                      rows="4"
                                      maxlength="500"
                                      class="w-full mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500">{{ old('footer', $report->footer) }}</textarea>

                            <div class="flex justify-between mt-1 text-xs text-gray-500">
                                <span>Max 500 characters</span>
                                <span><span id="footerCount">0</span>/500</span>
                            </div>
                        </section>

                    </div>

                    <!-- RIGHT (PREVIEW PANEL) -->
                    <aside class="lg:col-span-1">
                        <div class="sticky top-8 p-5 bg-white border border-gray-200 rounded-xl shadow-sm space-y-5">

                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">Live Preview</h3>
                                <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">Live</span>
                            </div>

                            <!-- Title -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Title</p>
                                <p id="previewTitle" class="mt-1 font-semibold text-gray-800">
                                    {{ old('title', $report->title) ?: 'Untitled Report' }}
                                </p>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Date Range</p>
                                <p id="previewRange" class="mt-1 text-gray-800">
                                    {{ old('from', optional($report->date_from)->format('Y-m-d')) }}
                                    →
                                    {{ old('to', optional($report->date_to)->format('Y-m-d')) }}
                                </p>
                            </div>

                            <!-- Settings -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Settings</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span id="previewMethod" class="px-2 py-1 text-xs border bg-gray-100 rounded-full">
                                        {{ $am=='cash' ? 'Cash Basis' : 'Accrual Basis' }}
                                    </span>

                                    <span id="previewRounding" class="px-2 py-1 text-xs border bg-gray-100 rounded-full">
                                        {{ $r=='off'?'No Rounding':($r=='nearest'?'Nearest':"Nearest $r") }}
                                    </span>
                                </div>
                            </div>

                            <!-- Footer Notes -->
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Footer</p>
                                <p id="previewFooter" class="mt-1 text-gray-800 break-words max-h-20 overflow-y-auto">
                                    {{ old('footer', $report->footer) ?: '—' }}
                                </p>
                            </div>

                        </div>
                    </aside>

                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('reports.financial.profit-and-loss.show', $report->id) }}"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
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

<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<!-- Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Date Pickers
    flatpickr("#from", { dateFormat: "Y-m-d", altInput: true, altFormat: "F j, Y" });
    flatpickr("#to",   { dateFormat: "Y-m-d", altInput: true, altFormat: "F j, Y" });

    // Character Counter
    const footer = document.getElementById('footerTextarea');
    const footerCount = document.getElementById('footerCount');
    footerCount.textContent = footer.value.length;
    footer.addEventListener('input', () => footerCount.textContent = footer.value.length);

    // Live Preview Updates
    document.getElementById('title').addEventListener('input', e => {
        previewTitle.textContent = e.target.value || "Untitled Report";
    });

    document.getElementById('from').addEventListener('change', updateRange);
    document.getElementById('to').addEventListener('change', updateRange);

    function updateRange() {
        previewRange.textContent = `${from.value} → ${to.value}`;
    }

});
</script>
</x-app-layout>
