<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Payment Form Container -->
            <div class="bg-white border border-gray-200 shadow rounded-xl p-8">

                <h1 class="text-3xl font-bold text-gray-900 mb-8">Record Payment</h1>

                <form method="POST" action="{{ route('payments.store') }}">
                    @csrf

                    <!-- Main Payment Inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Payment Date -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Payment Date</label>
                            <input type="date" name="payment_date"
                                   value="{{ old('payment_date', now()->toDateString()) }}"
                                   class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Customer -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Customer</label>
                            <select name="customer_id"
                                    class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>
                                        {{ $c->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Amount</label>
                            <input type="text" name="amount" placeholder="0.00"
                                   value="{{ old('amount') }}"
                                   class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Payment Type -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Payment Type</label>
                            <select name="payment_type"
                                    class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="Customer" @selected(old('payment_type','Customer')=='Customer')>Customer</option>
                                <option value="Supplier" @selected(old('payment_type')=='Supplier')>Supplier</option>
                            </select>
                        </div>

                        <!-- Reference -->
                        <div class="md:col-span-2">
                            <label class="block text-sm text-gray-700 font-medium mb-1">Reference</label>
                            <input type="text" name="reference"
                                   value="{{ old('reference') }}"
                                   class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-3">
                            <label class="block text-sm text-gray-700 font-medium mb-1">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                        </div>

                    </div>

                    <!-- Allocations Section -->
                    <div class="mt-10 border-t pt-8">

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Allocations (Optional)</h2>
                                <p class="text-sm text-gray-600">Apply this payment to specific invoices.</p>
                            </div>

                            <button type="button" id="addAllocation"
                                    class="px-4 py-2 text-white bg-green-600 hover:bg-green-700 rounded-lg shadow">
                                Add Allocation
                            </button>
                        </div>

                        <div id="allocationsContainer" class="space-y-4"></div>

                        <div class="mt-4 text-gray-900 text-sm">
                            <strong>Total allocated:</strong>
                            <span id="allocatedTotal" class="font-semibold">0.00</span>

                            <p id="allocError" class="mt-2 text-sm text-red-600 hidden">
                                Allocation exceeds payment amount.
                            </p>
                        </div>

                        <!-- Allocation Template -->
                        <template id="allocationTemplate">
                            <div class="grid grid-cols-12 gap-3 p-4 bg-gray-50 border border-gray-300 rounded-lg">

                                <!-- Invoice dropdown -->
                                <div class="col-span-7">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Invoice</label>
                                    <select data-name-invoice
                                            class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                                        <option value="">-- Select invoice --</option>
                                        @foreach($invoices as $inv)
                                            <option value="{{ $inv->id }}"
                                                data-customer="{{ $inv->customer_id }}"
                                                data-balance="{{ number_format($inv->balanceDue(),2,'.','') }}">
                                                {{ $inv->invoice_number }} — {{ number_format($inv->balanceDue(), 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Amount -->
                                <div class="col-span-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Amount</label>
                                    <input data-name-amount placeholder="0.00"
                                           class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900">
                                </div>

                                <!-- Remove button -->
                                <div class="col-span-2 flex items-end">
                                    <button type="button" data-remove
                                            class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-10 flex justify-end gap-3">

                        <a href="{{ route('customerinvoices.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
                            Save Payment
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <!-- JS (logic remains same, formatting improved) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const container = document.getElementById('allocationsContainer');
            const tmpl = document.getElementById('allocationTemplate');
            const addBtn = document.getElementById('addAllocation');
            let idx = 0;

            function addRow(invoiceId = '', amount = '') {
                const node = tmpl.content.cloneNode(true);
                const row = node.querySelector('div');

                const select = row.querySelector('[data-name-invoice]');
                const input = row.querySelector('[data-name-amount]');
                const remove = row.querySelector('[data-remove]');

                select.name = `allocations[${idx}][invoice_id]`;
                input.name = `allocations[${idx}][amount]`;

                select.value = invoiceId;
                input.value = amount;

                remove.addEventListener('click', () => { row.remove(); computeTotal(); });

                select.addEventListener('change', () => {
                    const bal = select.selectedOptions[0]?.dataset?.balance || 0;
                    if (!input.value) input.value = parseFloat(bal).toFixed(2);
                    computeTotal();
                });

                input.addEventListener('input', computeTotal);

                container.appendChild(row);
                filterInvoices(select);
                idx++;
                computeTotal();
            }

            function filterInvoices(select) {
                const customerId = document.querySelector('select[name="customer_id"]').value;
                Array.from(select.options).forEach(opt => {
                    if (!opt.value) return;
                    opt.hidden = customerId && opt.dataset.customer != customerId;
                });
            }

            function computeTotal() {
                const paymentAmount = parseFloat(document.querySelector('input[name="amount"]').value || 0);
                let allocated = 0;

                container.querySelectorAll('[data-name-amount]').forEach(inp => {
                    allocated += parseFloat(inp.value) || 0;
                });

                document.getElementById('allocatedTotal').textContent = allocated.toFixed(2);

                const err = document.getElementById('allocError');
                err.classList.toggle('hidden', allocated <= paymentAmount);
            }

            addBtn.addEventListener('click', () => addRow());

            const customerSelect = document.querySelector('select[name="customer_id"]');
            customerSelect.addEventListener('change', () => {
                container.querySelectorAll('[data-name-invoice]').forEach(filterInvoices);
                computeTotal();
            });

            const oldAlloc = @json(old('allocations', []));
            oldAlloc?.forEach(a => addRow(a.invoice_id, a.amount));
        });
    </script>

</x-app-layout>
