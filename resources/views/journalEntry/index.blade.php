<x-app-layout>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="mb-6">
				<h1 class="text-2xl font-bold text-gray-900">Create Journal Entry</h1>
				<p class="text-sm text-gray-600 mt-1">Record manual journal transactions</p>
			</div>

			<div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-lg p-6">
				@if(session('success'))
					<div class="mb-4 text-green-800 bg-green-100 border border-green-300 p-3 rounded">{{ session('success') }}</div>
				@endif

				@if($errors->any())
					<div class="mb-4">
						<div class="font-medium text-red-800">There were some problems with your input.</div>
						<ul class="mt-2 text-sm text-red-800 list-disc list-inside bg-red-100 border border-red-300 p-3 rounded">
							@foreach($errors->all() as $err)
								<li>{{ $err }}</li>
							@endforeach
						</ul>
					</div>
				@endif

						@if(isset($entry))
							<form action="{{ route('journal.update', $entry->id) }}" method="POST" id="journalForm">
							@csrf
							@method('PUT')
						@else
							<form action="{{ route('journal.store') }}" method="POST" id="journalForm">
							@csrf
						@endif

					<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
						<div>
							<label class="block text-sm font-medium text-gray-700">Entry Date</label>
							<input type="date" name="entry_date" value="{{ old('entry_date', isset($entry) && $entry->entry_date ? $entry->entry_date->format('Y-m-d') : date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700">Accounting Period</label>
							<select name="accounting_period_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
								<option value="">-- Select period (optional) --</option>
								@foreach($periods as $p)
									<option value="{{ $p->id }}" {{ (string)old('accounting_period_id', isset($entry) ? $entry->accounting_period_id : '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name ?? $p->start_date.' - '.$p->end_date }}</option>
								@endforeach
							</select>
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-700">Reference</label>
							<div class="flex gap-2">
								<input type="text" name="reference_type" placeholder="Type (e.g. Invoice)" value="{{ old('reference_type', $entry->reference_type ?? '') }}" class="mt-1 block w-1/2 border-gray-300 rounded-md shadow-sm">
								<input type="text" name="reference_id" placeholder="ID" value="{{ old('reference_id', $entry->reference_id ?? '') }}" class="mt-1 block w-1/2 border-gray-300 rounded-md shadow-sm">
							</div>
						</div>
					</div>

					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700">Description</label>
						<textarea name="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $entry->description ?? '') }}</textarea>
					</div>

					<div class="mb-4">
						<div class="flex items-center justify-between mb-2">
							<h3 class="font-semibold">Lines</h3>
							<div class="flex items-center gap-2">
								<button type="button" id="addLine" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded">Add line</button>
							</div>
						</div>

						<div class="overflow-x-auto h-60">
							<table class="min-w-full divide-y divide-gray-200" id="linesTable">
								<thead class="bg-gray-50">
									<tr>
										<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Account</th>
										<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
										<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Customer</th>
										<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Supplier</th>
										<th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Debit</th>
										<th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Credit</th>
										<th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Actions</th>
									</tr>
								</thead>
								<tbody class="bg-white divide-y divide-gray-200" id="linesBody">
									{{-- If old input exists, render those lines so users can fix validation errors --}}
									@if(old('lines'))
										@foreach(old('lines') as $ln)
											<tr>
												<td class="px-3 py-2">
													<select name="lines[][account_id]" class="w-full border-gray-300 rounded">
														<option value="">-- select account --</option>
														@foreach($accounts as $account)
															<option value="{{ $account->id }}" {{ (int)($ln['account_id'] ?? 0) === $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
														@endforeach
													</select>
												</td>
												<td class="px-3 py-2">
													<input type="text" name="lines[][description]" value="{{ $ln['description'] ?? '' }}" class="w-full border-gray-200 rounded" placeholder="Line description">
												</td>
												<td class="px-3 py-2">
													<select name="lines[][customer_id]" class="w-full border-gray-200 rounded">
														<option value="">-- none --</option>
														@foreach(($customers ?? []) as $c)
															<option value="{{ $c->id }}" {{ (int)($ln['customer_id'] ?? 0) === (int)$c->id ? 'selected' : '' }}>{{ $c->customer_name }}</option>
														@endforeach
													</select>
												</td>
												<td class="px-3 py-2">
													<select name="lines[][supplier_id]" class="w-full border-gray-200 rounded">
														<option value="">-- none --</option>
														@foreach(($suppliers ?? []) as $s)
															<option value="{{ $s->id }}" {{ (int)($ln['supplier_id'] ?? 0) === (int)$s->id ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
														@endforeach
													</select>
												</td>
												<td class="px-3 py-2 text-right">
													<input type="number" step="0.01" min="0" name="lines[][debit_amount]" value="{{ $ln['debit_amount'] ?? 0 }}" class="w-32 text-right border-gray-200 rounded line-amount debit">
												</td>
												<td class="px-3 py-2 text-right">
													<input type="number" step="0.01" min="0" name="lines[][credit_amount]" value="{{ $ln['credit_amount'] ?? 0 }}" class="w-32 text-right border-gray-200 rounded line-amount credit">
												</td>
												<td class="px-3 py-2 text-center">
													<button type="button" class="removeLine inline-flex items-center px-2 py-1 bg-red-600 text-white rounded">Remove</button>
												</td>
											</tr>
										@endforeach
									@elseif(isset($entry))
										@foreach($entry->lines as $ln)
											<tr>
												<td class="px-3 py-2">
													<select name="lines[][account_id]" class="w-full border-gray-300 rounded">
														<option value="">-- select account --</option>
														@foreach($accounts as $account)
															<option value="{{ $account->id }}" {{ (int)($ln->account_id ?? 0) === $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
														@endforeach
													</select>
												</td>
												<td class="px-3 py-2">
													<input type="text" name="lines[][description]" value="{{ $ln->description ?? '' }}" class="w-full border-gray-200 rounded" placeholder="Line description">
												</td>
												<td class="px-3 py-2">
													<select name="lines[][customer_id]" class="w-full border-gray-200 rounded">
														<option value="">-- none --</option>
														@foreach(($customers ?? []) as $c)
															<option value="{{ $c->id }}" {{ (int)($ln->customer_id ?? 0) === (int)$c->id ? 'selected' : '' }}>{{ $c->customer_name }}</option>
														@endforeach
													</select>
												</td>
												<td class="px-3 py-2">
													<select name="lines[][supplier_id]" class="w-full border-gray-200 rounded">
														<option value="">-- none --</option>
														@foreach(($suppliers ?? []) as $s)
															<option value="{{ $s->id }}" {{ (int)($ln->supplier_id ?? 0) === (int)$s->id ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
														@endforeach
													</select>
												</td>
												<td class="px-3 py-2 text-right">
													<input type="number" step="0.01" min="0" name="lines[][debit_amount]" value="{{ $ln->debit_amount ?? 0 }}" class="w-32 text-right border-gray-200 rounded line-amount debit">
												</td>
												<td class="px-3 py-2 text-right">
													<input type="number" step="0.01" min="0" name="lines[][credit_amount]" value="{{ $ln->credit_amount ?? 0 }}" class="w-32 text-right border-gray-200 rounded line-amount credit">
												</td>
												<td class="px-3 py-2 text-center">
													<button type="button" class="removeLine inline-flex items-center px-2 py-1 bg-red-600 text-white rounded">Remove</button>
												</td>
											</tr>
										@endforeach
									@endif
								</tbody>
								<tfoot>
									<tr class="bg-gray-50">
										<td colspan="4" class="px-3 py-2 text-right font-semibold">Totals</td>
										<td class="px-3 py-2 text-right font-semibold" id="totalDebit">0.00</td>
										<td class="px-3 py-2 text-right font-semibold" id="totalCredit">0.00</td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>

					<div class="flex items-center justify-end gap-3">
						<a href="{{ route('journal.index') }}" class="px-4 py-2 border rounded">Cancel</a>
						<button type="submit" id="submitBtn" class="px-4 py-2 bg-green-600 text-white rounded">Save Entry</button>
					</div>
				</form>

				<!-- Recent journal entries -->
				<div class="mt-8">
					<h3 class="font-semibold mb-3">Recent Journal Entries</h3>
					@if(isset($recentEntries) && $recentEntries->count())
						<table class="min-w-full divide-y divide-gray-200 bg-white rounded shadow-sm">
							<thead class="bg-gray-50">
								<tr>
									<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Date</th>
									<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Reference</th>
									<th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
									<th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total Debit</th>
									<th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Total Credit</th>
									<th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Actions</th>
								</tr>
							</thead>
							<tbody class="divide-y">
							@foreach($recentEntries as $entryRow)
								<tr>
									<td class="px-3 py-2">{{ $entryRow->entry_date->format('Y-m-d') }}</td>
									<td class="px-3 py-2">{{ $entryRow->reference_type }} {{ $entryRow->reference_id }}</td>
									<td class="px-3 py-2">{{ \Illuminate\Support\Str::limit($entryRow->description, 80) }}</td>
									<td class="px-3 py-2 text-right">{{ number_format($entryRow->lines->sum('debit_amount'), 2) }}</td>
									<td class="px-3 py-2 text-right">{{ number_format($entryRow->lines->sum('credit_amount'), 2) }}</td>
									<td class="px-3 py-2 text-center">
										<a href="{{ route('journal.show', $entryRow->id) }}" class="text-blue-600 mr-2">View</a>
										<a href="{{ route('journal.edit', $entryRow->id) }}" class="text-yellow-600 mr-2">Edit</a>
										<form action="{{ route('journal.destroy', $entryRow->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this journal entry?');">
											@csrf
											@method('DELETE')
											<button type="submit" class="text-red-600">Delete</button>
										</form>
									</td>
								</tr>
								<tr>
									<td colspan="6" class="px-3 py-2 bg-gray-50">
										<div class="text-sm font-medium mb-1">Lines</div>
										<table class="w-full text-sm">
											<thead>
												<tr class="text-left text-xs text-gray-500">
													<th class="px-1">Account</th>
													<th class="px-1 text-right">Debit</th>
													<th class="px-1 text-right">Credit</th>
													<th class="px-1">Desc</th>
												</tr>
											</thead>
											<tbody>
											@foreach($entryRow->lines as $ln)
												<tr>
													<td class="px-1">{{ optional($ln->account)->account_name }}</td>
													<td class="px-1 text-right">{{ number_format($ln->debit_amount,2) }}</td>
													<td class="px-1 text-right">{{ number_format($ln->credit_amount,2) }}</td>
													<td class="px-1">{{ \Illuminate\Support\Str::limit($ln->description ?? '', 50) }}</td>
												</tr>
											@endforeach
											</tbody>
										</table>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					@else
						<div class="text-sm text-gray-500">No recent entries.</div>
					@endif
				</div>

				<!-- template row (hidden) -->
				<template id="rowTemplate">
					<tr>
						<td class="px-3 py-2">
							<select name="lines[][account_id]" class="w-full border-gray-300 rounded">
								<option value="">-- select account --</option>
								@foreach($accounts as $account)
									<option value="{{ $account->id }}">{{ $account->account_name }}</option>
								@endforeach
							</select>
						</td>
						<td class="px-3 py-2">
							<input type="text" name="lines[][description]" class="w-full border-gray-200 rounded" placeholder="Line description">
						</td>
						<td class="px-3 py-2">
							<select name="lines[][customer_id]" class="w-full border-gray-200 rounded">
								<option value="">-- none --</option>
								@foreach(($customers ?? []) as $c)
									<option value="{{ $c->id }}">{{ $c->customer_name }}</option>
								@endforeach
							</select>
						</td>
						<td class="px-3 py-2">
							<select name="lines[][supplier_id]" class="w-full border-gray-200 rounded">
								<option value="">-- none --</option>
								@foreach(($suppliers ?? []) as $s)
									<option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
								@endforeach
							</select>
						</td>
						<td class="px-3 py-2 text-right">
							<input type="number" step="0.01" min="0" name="lines[][debit_amount]" value="0" class="w-32 text-right border-gray-200 rounded line-amount debit">
						</td>
						<td class="px-3 py-2 text-right">
							<input type="number" step="0.01" min="0" name="lines[][credit_amount]" value="0" class="w-32 text-right border-gray-200 rounded line-amount credit">
						</td>
						<td class="px-3 py-2 text-center">
							<button type="button" class="removeLine inline-flex items-center px-2 py-1 bg-red-600 text-white rounded">Remove</button>
						</td>
					</tr>
				</template>
			</div>
		</div>
	</div>

	<script>
		(function(){
			const addLineBtn = document.getElementById('addLine');
			const linesBody = document.getElementById('linesBody');
			const template = document.getElementById('rowTemplate');
			const totalDebitEl = document.getElementById('totalDebit');
			const totalCreditEl = document.getElementById('totalCredit');
			const form = document.getElementById('journalForm');
			const submitBtn = document.getElementById('submitBtn');

			function addRow(prefill = {}){
				const clone = template.content.cloneNode(true);
				const tr = clone.querySelector('tr');

				// set values safely
				const accSel = tr.querySelector('select[name$="[account_id]"]');
				if (accSel && prefill.account_id) accSel.value = prefill.account_id;

				const descInput = tr.querySelector('input[name$="[description]"]');
				if (descInput) descInput.value = prefill.description ?? '';

				const custSel = tr.querySelector('select[name$="[customer_id]"]');
				if (custSel && prefill.customer_id) custSel.value = prefill.customer_id;

				const supSel = tr.querySelector('select[name$="[supplier_id]"]');
				if (supSel && prefill.supplier_id) supSel.value = prefill.supplier_id;

				const debitInput = tr.querySelector('input[name$="[debit_amount]"]');
				const creditInput = tr.querySelector('input[name$="[credit_amount]"]');
				if (debitInput) debitInput.value = (prefill.debit_amount !== undefined) ? parseFloat(prefill.debit_amount) : 0;
				if (creditInput) creditInput.value = (prefill.credit_amount !== undefined) ? parseFloat(prefill.credit_amount) : 0;

				linesBody.appendChild(tr);
				// no per-row listeners needed — use delegation below
				recalcTotals();
			}

			function recalcTotals(){
				let totalDebit = 0;
				let totalCredit = 0;
				const rows = Array.from(linesBody.querySelectorAll('tr'));
				rows.forEach(r => {
					const dIn = r.querySelector('input[name$="[debit_amount]"]');
					const cIn = r.querySelector('input[name$="[credit_amount]"]');
					const d = dIn ? (parseFloat(dIn.value) || 0) : 0;
					const c = cIn ? (parseFloat(cIn.value) || 0) : 0;
					totalDebit += d;
					totalCredit += c;
				});

				totalDebitEl.textContent = totalDebit.toFixed(2);
				totalCreditEl.textContent = totalCredit.toFixed(2);

				// disable submit if not balanced or less than 2 lines
				const count = rows.length;
				const balanced = Math.abs(totalDebit - totalCredit) < 0.005 && count >= 2;
				submitBtn.disabled = !balanced;
				submitBtn.classList.toggle('opacity-50', !balanced);
			}

			// delegation: handle inputs and remove clicks from existing and future rows
			linesBody.addEventListener('input', function(e){
				if (e.target && e.target.matches('.line-amount')) {
					recalcTotals();
				}
			});

			linesBody.addEventListener('click', function(e){
				if (e.target && e.target.matches('.removeLine')) {
					const row = e.target.closest('tr');
					if (row) {
						row.remove();
						recalcTotals();
					}
				}
			});

			addLineBtn.addEventListener('click', () => addRow());

			// initialize: if server rendered rows exist, ensure numeric values and recalc; otherwise seed two
			const existingRows = linesBody.querySelectorAll('tr').length;
			if (existingRows === 0) {
				addRow(); addRow();
			} else {
				// coerce values on any existing rows to numeric defaults
				linesBody.querySelectorAll('tr').forEach(r => {
					const dIn = r.querySelector('input[name$="[debit_amount]"]');
					const cIn = r.querySelector('input[name$="[credit_amount]"]');
					if (dIn && dIn.value === '') dIn.value = 0;
					if (cIn && cIn.value === '') cIn.value = 0;
				});
				recalcTotals();
			}

			// sanitize rows and prevent submit if not balanced
			function sanitizeAndReindexRows(){
				const rows = Array.from(linesBody.querySelectorAll('tr'));
				let idx = 0;
				rows.forEach(r => {
					const acc = r.querySelector('select[name$="[account_id]"]');
					const dIn = r.querySelector('input[name$="[debit_amount]"]');
					const cIn = r.querySelector('input[name$="[credit_amount]"]');
					const desc = r.querySelector('input[name$="[description]"]');
					const cust = r.querySelector('select[name$="[customer_id]"]');
					const sup = r.querySelector('select[name$="[supplier_id]"]');

					const accVal = acc ? acc.value.trim() : '';
					const dVal = dIn ? (parseFloat(dIn.value) || 0) : 0;
					const cVal = cIn ? (parseFloat(cIn.value) || 0) : 0;
					const descVal = desc ? desc.value.trim() : '';
					const custVal = cust ? cust.value.trim() : '';
					const supVal = sup ? sup.value.trim() : '';

					const keep = (accVal !== '' || dVal !== 0 || cVal !== 0 || descVal !== '' || custVal !== '' || supVal !== '');
					if (!keep) {
						r.remove();
					} else {
						if (acc) acc.name = `lines[${idx}][account_id]`;
						if (desc) desc.name = `lines[${idx}][description]`;
						if (cust) cust.name = `lines[${idx}][customer_id]`;
						if (sup) sup.name = `lines[${idx}][supplier_id]`;
						if (dIn) dIn.name = `lines[${idx}][debit_amount]`;
						if (cIn) cIn.name = `lines[${idx}][credit_amount]`;
						idx++;
					}
				});
			}

			form.addEventListener('submit', function(e){
				// remove empty rows and reindex so server receives contiguous indices
				sanitizeAndReindexRows();
				// refresh totals after sanitization
				recalcTotals();

				const d = parseFloat(totalDebitEl.textContent) || 0;
				const c = parseFloat(totalCreditEl.textContent) || 0;
				const rowsCount = linesBody.querySelectorAll('tr').length;
				if (rowsCount < 2) {
					e.preventDefault();
					alert('Please provide at least two lines.');
					return;
				}

				if (Math.abs(d - c) > 0.005){
					e.preventDefault();
					alert('Total debits must equal total credits.');
				}
			});
		})();
	</script>
</x-app-layout>