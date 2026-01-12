<x-app-layout>
    <div class="py-6 text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 p-6 rounded">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-600 text-white rounded">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-600 text-white rounded">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('payments.store') }}">
                    @csrf

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Date</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Customer</label>
                            <select name="customer_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option class="text-black" value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Amount</label>
                            <input type="text" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Payment Type</label>
                                <select name="payment_type" class="w-full border rounded px-3 py-2 text-white">
                                    <option class="text-black" value="Customer" @selected(old('payment_type','Customer')=='Customer')>Customer</option>
                                    <option class="text-black" value="Supplier" @selected(old('payment_type')=='Supplier')>Supplier</option>
                                </select>
                        </div>

                        <div class="col-span-12">
                            <label class="block text-sm text-white">Reference</label>
                            <input type="text" name="reference" value="{{ old('reference') }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>

                        <div class="col-span-12">
                            <label class="block text-sm text-white">Notes</label>
                            <textarea name="notes" class="w-full border rounded px-3 py-2 text-white">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-700 mt-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-200">Allocations (optional)</h3>
                                <p class="text-xs text-white">Apply this payment to one or more invoices.</p>
                            </div>
                            <button type="button" id="addAllocation" class="px-3 py-1 bg-green-600 rounded text-sm">Add Allocation</button>
                        </div>

                        <div id="allocationsContainer" class="mt-3 space-y-2">
                            <!-- dynamic rows go here -->
                        </div>

                        <div class="mt-2 text-sm">
                            <span class="text-gray-300">Total allocated: </span>
                            <span id="allocatedTotal" class="font-medium">0.00</span>
                            <p id="allocError" class="text-red-400 text-sm mt-1 hidden">Total allocation amount exceeds payment amount.</p>
                        </div>

                        <template id="allocationTemplate">
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-6">
                                    <select data-name-invoice class="w-full border rounded px-3 py-2 text-white">
                                        <option value="">-- Select invoice --</option>
                                        @foreach($invoices as $inv)
                                            <option value="{{ $inv->id }}" data-customer="{{ $inv->customer_id }}" data-balance="{{ number_format($inv->balanceDue(),2,'.','') }}">{{ $inv->invoice_number }} — {{ $inv->customer?->customer_name ?? '' }} — {{ number_format($inv->balanceDue(),2) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-4">
                                    <input data-name-amount placeholder="Amount" class="w-full border rounded px-3 py-2 text-white">
                                </div>
                                <div class="col-span-2">
                                    <button type="button" data-remove class="px-3 py-2 bg-red-600 rounded">Remove</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('customerinvoices.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // dynamic allocations
            const container = document.getElementById('allocationsContainer');
            const tmpl = document.getElementById('allocationTemplate');
            const addBtn = document.getElementById('addAllocation');
            let idx = 0;

            function addRow(invoiceId = '', amount = ''){
                if (!tmpl) return;
                const node = tmpl.content.cloneNode(true);
                const row = node.querySelector('div');
                const select = node.querySelector('[data-name-invoice]');
                const input = node.querySelector('[data-name-amount]');
                const remove = node.querySelector('[data-remove]');

                select.name = `allocations[${idx}][invoice_id]`;
                input.name = `allocations[${idx}][amount]`;
                select.value = invoiceId;
                input.value = amount;

                remove.addEventListener('click', function(){ row.remove(); computeTotal(); });

                // when invoice select changes, auto-fill amount with invoice balance
                select.addEventListener('change', function(){
                    const opt = select.selectedOptions[0];
                    if (opt && opt.dataset && opt.dataset.balance) {
                        // only set amount if empty or zero
                        const val = parseFloat((input.value||0)) || 0;
                        const bal = parseFloat(opt.dataset.balance) || 0;
                        if (!val || val === 0) input.value = bal.toFixed(2);
                    }
                    computeTotal();
                });

                container.appendChild(node);
                // attach input listener to update totals
                input.addEventListener('input', computeTotal);
                // apply customer filter to this select immediately
                applyCustomerFilterToSelect(select);
                idx++;
                computeTotal();
            }

            function applyCustomerFilterToSelect(select){
                const cust = (document.querySelector('select[name="customer_id"]') || {}).value || '';
                Array.from(select.options).forEach(opt => {
                    if (!opt.value) return; // keep placeholder
                    if (!cust) {
                        opt.hidden = false;
                        opt.disabled = false;
                        return;
                    }
                    const oCust = opt.dataset.customer || '';
                    if (oCust.toString() === cust.toString()){
                        opt.hidden = false;
                        opt.disabled = false;
                    } else {
                        opt.hidden = true;
                        opt.disabled = true;
                    }
                });
            }

            function applyCustomerFilterToAll(){
                const selects = container.querySelectorAll('[data-name-invoice]');
                selects.forEach(s => applyCustomerFilterToSelect(s));
                // clear selections that are no longer valid
                selects.forEach(s => {
                    const sel = s.selectedOptions[0];
                    if (sel && sel.disabled) {
                        s.value = '';
                        const input = s.closest('div').querySelector('[data-name-amount]');
                        if (input) input.value = '';
                    }
                });
                computeTotal();
            }

            addBtn && addBtn.addEventListener('click', function(){ addRow(); });

            // when customer changes, filter invoice options in all allocation selects
            const customerSelect = document.querySelector('select[name="customer_id"]');
            if (customerSelect) {
                customerSelect.addEventListener('change', function(){
                    applyCustomerFilterToAll();
                });
            }

            function computeTotal(){
                const amountField = document.querySelector('input[name="amount"]');
                const paymentAmount = parseFloat((amountField && amountField.value) || 0) || 0;
                let sum = 0;
                container.querySelectorAll('[data-name-amount]').forEach(inp => {
                    const v = parseFloat(inp.value) || 0;
                    sum += v;
                });

                const totalEl = document.getElementById('allocatedTotal');
                if (totalEl) totalEl.textContent = sum.toFixed(2);

                const err = document.getElementById('allocError');
                if (err) {
                    if (sum > paymentAmount + 0.0001) {
                        err.classList.remove('hidden');
                    } else {
                        err.classList.add('hidden');
                    }
                }
                return {sum, paymentAmount};
            }

            // validate on submit
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e){
                    const {sum, paymentAmount} = computeTotal();
                    if (sum > paymentAmount + 0.0001) {
                        e.preventDefault();
                        const err = document.getElementById('allocError');
                        if (err) err.classList.remove('hidden');
                        window.scrollTo({top: err ? err.offsetTop - 80 : 0, behavior: 'smooth'});
                    }
                });
            }

            // if old input exists (after validation), re-populate allocations
            const oldAlloc = @json(old('allocations', []));
            if (Array.isArray(oldAlloc) && oldAlloc.length){
                oldAlloc.forEach(a => addRow(a.invoice_id ?? '', a.amount ?? ''));
            }
        });
    </script>
</x-app-layout>
