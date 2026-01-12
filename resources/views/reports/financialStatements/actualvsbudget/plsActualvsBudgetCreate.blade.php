<x-app-layout>
<div class="min-h-screen p-6 bg-gray-50 text-gray-900">

    <div class="max-w-4xl mx-auto">

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">

            <div>
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    Profit & Loss (Actual vs Budget)
                    <i class="far fa-question-circle text-gray-400"></i>
                </h1>
                <p class="text-sm text-gray-600">Create and configure a new Actual vs Budget report</p>
            </div>

            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-gray-300 
                      text-gray-700 hover:bg-gray-100 transition text-sm shadow-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>

        </div>


        <!-- FORM CARD -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            <form action="{{ route('reports.financial.profit-and-loss.actual-and-budget.store') }}"
                  method="POST"
                  class="p-6 space-y-5">

                @csrf


                <!-- TITLE -->
                <div class="space-y-1">
                    <label for="title" class="block text-sm font-semibold text-gray-800">Title</label>

                    <input id="title"
                           type="text"
                           name="title"
                           value="{{ old('title', 'Profit and Loss Statement (Actual vs Budget)') }}"
                           class="w-full max-w-md px-3 py-2 rounded-lg border border-gray-300 bg-white
                                  text-gray-900 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    @error('title')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <!-- DATE RANGE -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-900">
                        Date Range
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <!-- FROM DATE -->
                        <div>
                            <label class="text-xs text-gray-700">From</label>
                            <input type="text"
                                   id="from"
                                   name="from"
                                   value="{{ old('from', date('Y-m-d')) }}"
                                   placeholder="Select date"
                                   autocomplete="off"
                                   class="cursor-pointer mt-1 p-2 w-full bg-white border border-gray-300 rounded-lg
                                          shadow-sm text-gray-900"
                                   required>
                        </div>

                        <!-- TO DATE -->
                        <div>
                            <label class="text-xs text-gray-700">To</label>
                            <input type="text"
                                   id="to"
                                   name="to"
                                   value="{{ old('to', date('Y-m-d')) }}"
                                   placeholder="Select date"
                                   autocomplete="off"
                                   class="cursor-pointer mt-1 p-2 w-full bg-white border border-gray-300 rounded-lg
                                          shadow-sm text-gray-900"
                                   required>

                            <p id="clientDateError" class="text-xs text-red-600 mt-1 hidden">
                                From date cannot be after To date.
                            </p>
                        </div>

                    </div>
                </div>


                <!-- ACCOUNTING METHOD -->
                <div class="space-y-1 max-w-xs">
                    <label class="block text-sm font-semibold text-gray-900">
                        Accounting Method
                    </label>

                    <select id="accounting_method"
                            name="accounting_method"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900
                                   shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="accrual" {{ old('accounting_method', 'accrual') === 'accrual' ? 'selected' : '' }}>
                            Accrual basis
                        </option>
                        <option value="cash" {{ old('accounting_method') === 'cash' ? 'selected' : '' }}>
                            Cash basis
                        </option>
                    </select>
                </div>


                <!-- ACCOUNT LINES -->
                <div class="space-y-3">

                    <label class="text-sm font-semibold text-gray-900">Account Lines</label>

                    <!-- HEADER ROW -->
                    <div class="grid grid-cols-12 gap-2 text-xs font-medium text-gray-600 border-b pb-1">
                        <div class="col-span-7">Account</div>
                        <div class="col-span-4">Amount</div>
                        <div class="col-span-1"></div>
                    </div>

                    @php
                        $oldLines = old('lines');
                        if (!is_array($oldLines) || empty($oldLines)) {
                            $oldLines = [[ 'account_id' => '', 'amount' => 0 ]];
                        }
                    @endphp

                    <div id="linesContainer" class="space-y-2">
                        @foreach($oldLines as $i => $line)
                        <div class="grid grid-cols-12 gap-2 items-center">

                            <!-- Account Select -->
                            <div class="col-span-7">
                                <select name="lines[{{ $i }}][account_id]"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900
                                               shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}"
                                            {{ (string)($line['account_id']) === (string)$acc->id ? 'selected' : '' }}>
                                            {{ $acc->name ?? $acc->account_name ?? ('Account #' . $acc->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount Field -->
                            <div class="col-span-4">
                                <div class="flex items-center gap-2">
                                    <input type="number"
                                           name="lines[{{ $i }}][amount]"
                                           value="{{ $line['amount'] ?? 0 }}"
                                           step="0.01"
                                           class="flex-1 px-3 py-2 rounded-lg border border-gray-300 bg-white text-right
                                                  shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                    <button type="button"
                                            class="amount-tool w-9 h-9 rounded-lg border border-gray-300 bg-white
                                                   text-gray-600 hover:bg-gray-100">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remove Line -->
                            <div class="col-span-1 flex justify-end">
                                <button type="button"
                                        class="remove-line w-9 h-9 rounded-lg border border-gray-300 bg-white
                                               text-gray-600 hover:bg-gray-100">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    <button id="addLineBtn" type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-300 bg-white
                               text-gray-900 text-sm hover:bg-gray-100 transition shadow-sm">
                        <i class="fas fa-plus"></i> Add line
                    </button>

                </div>


                <!-- FOOTER NOTES -->
                <div class="space-y-1 max-w-3xl">
                    <label class="block text-sm font-semibold text-gray-900">
                        Footer Notes
                    </label>

                    <textarea id="footerTextarea"
                              name="footer"
                              rows="4"
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900
                                     shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>


                <!-- SUBMIT BUTTON -->
                <div class="pt-4 border-t border-gray-200">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 transition">
                        Create Report
                    </button>
                </div>

            </form>
        </div>


        <!-- JS SECTION -->
        <script>
        // DATE VALIDATION
        function validateDates() {
            const from = document.querySelector("#from").value;
            const to = document.querySelector("#to").value;
            const error = document.querySelector("#clientDateError");

            if (from && to && from > to) {
                error.classList.remove("hidden");
            } else {
                error.classList.add("hidden");
            }
        }

        // FLATPICKR INIT
        flatpickr("#from", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            defaultDate: "{{ old('from', date('Y-m-d')) }}",
            onChange: validateDates
        });

        flatpickr("#to", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            allowInput: true,
            defaultDate: "{{ old('to', date('Y-m-d')) }}",
            onChange: validateDates
        });


        // DYNAMIC ACCOUNT LINE HANDLING
        (function() {
            const container = document.getElementById('linesContainer');
            const addBtn = document.getElementById('addLineBtn');
            let index = container.children.length;

            addBtn.addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = "grid grid-cols-12 gap-2 items-center";

                row.innerHTML = `
                    <div class="col-span-7">
                        <select name="lines[${index}][account_id]"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 shadow-sm">
                            ${container.querySelector("select").innerHTML}
                        </select>
                    </div>

                    <div class="col-span-4">
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" value="0"
                                   name="lines[${index}][amount]"
                                   class="flex-1 px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 text-right shadow-sm">

                            <button type="button"
                                    class="amount-tool w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-100">
                                <i class="fas fa-calculator"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-span-1 flex justify-end">
                        <button type="button"
                                class="remove-line w-9 h-9 rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-100">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                `;

                container.appendChild(row);

                row.querySelector('.remove-line').addEventListener('click', () => row.remove());
                row.querySelector('.amount-tool').addEventListener('click', function() {
                    const input = row.querySelector("input[type='number']");
                    input.value = parseFloat(input.value || 0).toFixed(2);
                });

                index++;
            });

        })();
        </script>

    </div>

</div>
</x-app-layout>
