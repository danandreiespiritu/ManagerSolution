<x-app-layout>
	<div class="py-8">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

			<!-- HEADER -->
			<div class="mb-6">
				<h1 class="text-3xl font-bold text-gray-900">Create Journal Entry</h1>
				<p class="text-gray-600 mt-1">Record and review manual journal transactions</p>
			</div>

			<!-- MAIN CARD -->
			<div class="bg-white border border-gray-200 shadow-lg rounded-xl p-6">

				{{-- SUCCESS --}}
				@if(session('success'))
				<div class="mb-4 bg-green-50 text-green-700 border border-green-300 p-3 rounded-md">
					{{ session('success') }}
				</div>
				@endif

				{{-- ERRORS --}}
				@if($errors->any())
				<div class="mb-4 bg-red-50 border border-red-200 p-4 rounded-md">
					<div class="text-red-800 font-semibold mb-2">Please correct the following:</div>
					<ul class="text-red-700 text-sm list-disc ml-6">
						@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				{{-- FORM --}}
				@if(isset($entry))
				<form action="{{ route('journal.update', $entry->id) }}" method="POST" id="journalForm">
					@csrf @method('PUT')
					@else
					<form action="{{ route('journal.store') }}" method="POST" id="journalForm">
						@csrf
						@endif

						<!-- GRID TOP -->
						<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

							<!-- Entry Date -->
							<div>
								<label class="block text-sm font-medium text-gray-700">Entry Date</label>
								<input type="date"
									name="entry_date"
									class="mt-1 block w-full border-1 p-2 border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
									value="{{ old('entry_date', isset($entry) ? $entry->entry_date->format('Y-m-d') : date('Y-m-d')) }}">
							</div>

							<!-- Accounting Period -->
							<div>
								<label class="block text-sm font-medium text-gray-700 mb-1">Accounting Period</label>
								<select name="accounting_period_id"
									class="mt-1 block w-full border-1 p-2 border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
									<option value="">-- Select period --</option>
									@foreach($periods as $p)
									<option value="{{ $p->id }}"
										data-start-date="{{ $p->start_date }}"
										data-end-date="{{ $p->end_date }}"
										{{ old('accounting_period_id', $entry->accounting_period_id ?? '') == $p->id ? 'selected' : '' }}>
										{{ $p->name ?? ($p->start_date . ' - ' . $p->end_date) }}
									</option>
									@endforeach
								</select>
							</div>

							<!-- Reference -->
							<div>
								<label class="block text-sm font-medium text-gray-700">Reference</label>
								<div class="flex gap-3">
									<input type="text" name="reference_type" placeholder="Type"
										value="{{ old('reference_type', $entry->reference_type ?? '') }}"
										class="mt-1 block w-1/2 border-1 p-2 border-slate-300 rounded-lg shadow-sm">
									<input type="number" name="reference_id" placeholder="ID" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
										value="{{ old('reference_id', $entry->reference_id ?? '') }}"
										class="mt-1 block w-1/2 border-1 p-2 border-slate-300 rounded-lg shadow-sm">
								</div>
							</div>
						</div>

						<!-- DESCRIPTION -->
						<div class="mb-6">
							<label class="block text-sm font-medium text-gray-700">Description</label>
							<textarea name="description" rows="2"
								class="mt-1 block w-full border-1 p-2 border-slate-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $entry->description ?? '') }}</textarea>
						</div>


						<!-- LINES SECTION -->
						<div class="mb-6">

							<div class="flex items-center justify-between mb-3">
								<h3 class="font-semibold text-lg">Journal Lines</h3>
								<button type="button" id="addLine"
									class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm">
									Add Line
								</button>
							</div>

							<div class="overflow-auto max-h-72 border-1 border-slate-300 rounded-lg">
								<table class="min-w-full text-sm" id="linesTable">
									<thead class="bg-gray-100  sticky top-0 z-10">
										<tr>
											<th class="px-3 py-2 text-left border-b-1 border-slate-300">Account</th>
											<th class="px-3 py-2 text-left border-b-1 border-slate-300" id="cashCategoryHeader">Cash Category</th>
											<th class="px-3 py-2 text-left border-b-1 border-slate-300">Code</th>
											<th class="px-3 py-2 text-right border-b-1 border-slate-300">Debit</th>
											<th class="px-3 py-2 text-right border-b-1 border-slate-300">Credit</th>
											<th class="px-3 py-2 text-center border-b-1 border-slate-300">Actions</th>
										</tr>
									</thead>

									<tbody id="linesBody" class="divide-y">
										{{-- Laravel will render rows here --}}
										@if(old('lines'))
										@foreach(old('lines') as $ln)
										@include('partials.journal-line-row', ['ln' => $ln])
										@endforeach
										@elseif(isset($entry))
										@foreach($entry->lines as $ln)
										@include('partials.journal-line-row', ['ln' => (array)$ln])
										@endforeach
										@endif
									</tbody>

									<tfoot>
										<tr class="bg-gray-50 font-semibold">
											<td colspan="2" class="px-3 py-2 text-right">Totals</td>
											<td id="totalDebit" class="px-3 py-2 text-right">0.00</td>
											<td id="totalCredit" class="px-3 py-2 text-right">0.00</td>
											<td></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>

						<!-- ACTION BUTTONS -->
						<div class="flex justify-end gap-4 pt-4 border-t">
							<a href="{{ route('journal.index') }}"
								class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg">
								Cancel
							</a>

							<button type="submit" id="submitBtn"
								class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
								Save Entry
							</button>
						</div>

					</form>
			</div>

			<!-- RECENT ENTRIES SECTION -->
			<div class="mt-10">
				<div class="flex items-center justify-between mb-3 gap-4">
					<h3 class="text-xl font-semibold">Recent Journal Entries</h3>

					<div class="relative w-full max-w-xl ">
						<input id="searchInput"
							type="text"
							placeholder="Search entries..."
							class="w-full pl-10 pr-3 py-2 text-sm bg-white
             border border-slate-300 rounded-lg
             focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500" />

						<!-- Search Icon -->
						<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
							fill="none" stroke="currentColor" stroke-width="2"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="m21 21-4.35-4.35M16 10a6 6 0 1 1-12 0 6 6 0 0 1 12 0z" />
						</svg>
					</div>
				</div>

				@if(isset($recentEntries) && $recentEntries->count())
				<div class="bg-white rounded-lg shadow overflow-hidden">

					<!-- WRAPPER for AJAX update -->
					<div id="journalTableWrapper">
						<table class="min-w-full text-sm">
							<thead class="bg-gray-100">
								<tr>
									<th class="px-3 py-2 text-left">Date</th>
									<th class="px-3 py-2 text-left">Reference</th>
									<th class="px-3 py-2 text-left">Code</th>
									<th class="px-3 py-2 text-right">Debit</th>
									<th class="px-3 py-2 text-right">Credit</th>
									<th class="px-3 py-2 text-center">Status</th>
									<th class="px-3 py-2 text-center">Actions</th>
								</tr>
							</thead>

							<tbody class="divide-y" id="journal-table-body">
								@include('journalEntry.partials.entries-table', ['recentEntries' => $recentEntries])
							</tbody>
						</table>

						<!-- pagination -->
						<div class="p-4 pagination-wrapper">
							{{ $recentEntries->links() }}
						</div>
					</div>
				</div>
				@else
				<p class="text-gray-500">No recent entries found.</p>
				@endif

			</div>
		</div>
	</div>


	<!-- Updated search and display function -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const searchInput = document.getElementById('searchInput');
			const entriesTableBody = document.getElementById('journal-table-body');
			const entriesPagination = document.getElementById('entriesPagination');

			let currentSearch = '';

			function fetchEntries(url = null) {
				const params = new URLSearchParams();
				if (currentSearch) params.append('search', currentSearch);

				let fetchUrl = url || `${window.location.pathname}?${params.toString()}`;

				fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
					.then(res => res.json())
					.then(data => {
						entriesTableBody.innerHTML = data.entries;
						entriesPagination.innerHTML = data.pagination;
						attachPaginationLinks(); // Re-attach event listeners after updating links
					});
			}

			// Instant search
			searchInput.addEventListener('input', function() {
				currentSearch = this.value.trim();
				fetchEntries();
			});

			// Attach click listeners to pagination links
			function attachPaginationLinks() {
				entriesPagination.querySelectorAll('a').forEach(link => {
					link.addEventListener('click', function(e) {
						e.preventDefault();
						const url = this.getAttribute('href');
						if (url) fetchEntries(url);
					});
				});
			}

			// Initial attachment
			attachPaginationLinks();
		});
	</script>

	<!-- TEMPLATE ROW FOR JS -->
	<template id="rowTemplate">
		<tr>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const periodSelect = document.querySelector('select[name="accounting_period_id"]');
			const entryDateInput = document.querySelector('input[name="entry_date"]');

			if (! periodSelect || ! entryDateInput) return;

			function syncEntryDateWithPeriod() {
				const opt = periodSelect.selectedOptions[0];
				if (! opt || ! opt.dataset.startDate) {
					entryDateInput.removeAttribute('min');
					entryDateInput.removeAttribute('max');
					return;
				}

				const start = opt.dataset.startDate;
				const end = opt.dataset.endDate;
				entryDateInput.setAttribute('min', start);
				entryDateInput.setAttribute('max', end);

				const current = entryDateInput.value;
				if (! current || current < start || current > end) {
					entryDateInput.value = start;
				}
			}

			periodSelect.addEventListener('change', syncEntryDateWithPeriod);
			// initial sync on load
			syncEntryDateWithPeriod();
		});
	</script>
			<td class="px-3 py-2">
				<select name="lines[][account_id]" class="account-select w-full border-gray-300 rounded">
					<option value="">-- select account --</option>
					@foreach($accounts as $account)
					<option value="{{ $account->id }}" data-code="{{ $account->account_code }}">
						{{ $account->account_name }}
					</option>
					@endforeach
				</select>
			</td>
			
		<td class="px-3 py-2">
			<select name="lines[][cash_category]" class="cash-category w-full border-gray-300 rounded">
				<option value="">None / Select category</option>
				<option value="Operating activities">Operating activities</option>
				<option value="Investing activities">Investing activities</option>
				<option value="Financing activities">Financing activities</option>
			</select>
		</td>
			<td class="px-3 py-2">
				<input type="text" name="lines[][account_code]" class="code-input w-full border-gray-200 rounded bg-gray-100 py-1 px-2" readonly placeholder="-- code --">
			</td>
			<td class="px-3 py-2 text-right">
				<input type="number" name="lines[][debit_amount]" value="0.00" step="0.01" min="0"
					class="w-32 text-right border-gray-200 rounded line-amount debit">
			</td>
			<td class="px-3 py-2 text-right">
				<input type="number" name="lines[][credit_amount]" value="0.00" step="0.01" min="0"
					class="w-32 text-right border-gray-200 rounded line-amount credit">
			</td>
			<td class="px-3 py-2 text-center">
				<button type="button"
					class="removeLine px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded">
					Remove
				</button>
			</td>
		</tr>
	</template>

	<!-- Delete Confirmation Modal -->
	<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
		<div class="absolute inset-0 bg-black/50" id="deleteModalOverlay"></div>
		<div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4 z-10">
			<div class="p-4 border-b">
				<h3 class="text-lg font-semibold">Confirm deletion</h3>
			</div>
			<div class="p-4">
				<p class="text-sm text-gray-700">Are you sure you want to delete this journal entry? This action cannot be undone.</p>
			</div>
			<div class="p-4 flex justify-end gap-3 border-t">
				<button id="deleteCancel" type="button" class="px-4 py-2 bg-gray-100 rounded">Cancel</button>
				<button id="deleteConfirm" type="button" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
			</div>
		</div>
	</div>

	<!-- JAVASCRIPT (unchanged except formatting) -->
	<script>
		(function() {
			const addLineBtn = document.getElementById('addLine');
			const linesBody = document.getElementById('linesBody');
			const template = document.getElementById('rowTemplate');
			const totalDebitEl = document.getElementById('totalDebit');
			const totalCreditEl = document.getElementById('totalCredit');
			const form = document.getElementById('journalForm');
			const submitBtn = document.getElementById('submitBtn');

			function addRow(prefill = {}) {
						const clone = template.content.cloneNode(true);
						const tr = clone.querySelector('tr');

						// Prefill values
						tr.querySelector('[name$="[account_id]"]').value = prefill.account_id ?? "";
						tr.querySelector('[name$="[account_code]"]').value = prefill.account_code ?? "";
						tr.querySelector('[name$="[cash_category]"]').value = prefill.cash_category ?? "";
						tr.querySelector('[name$="[debit_amount]"]').value = prefill.debit_amount ?? 0;
						tr.querySelector('[name$="[credit_amount]"]').value = prefill.credit_amount ?? 0;

						linesBody.appendChild(tr);
						recalcTotals();
			}

			function recalcTotals() {
				let debit = 0;
				let credit = 0;

				linesBody.querySelectorAll('tr').forEach(row => {
					const d = parseFloat(row.querySelector('[name$="[debit_amount]"]').value) || 0;
					const c = parseFloat(row.querySelector('[name$="[credit_amount]"]').value) || 0;
					debit += d;
					credit += c;
				});

				totalDebitEl.textContent = debit.toFixed(2);
				totalCreditEl.textContent = credit.toFixed(2);

				// submitBtn.disabled = Math.abs(debit - credit) > 0.005;
				submitBtn.classList.toggle('opacity-50', submitBtn.disabled);
			}

					function normalizeRows() {
						let idx = 0;
						linesBody.querySelectorAll('tr').forEach(row => {
							const acc = row.querySelector('select[name$="[account_id]"]');
							const codeIn = row.querySelector('input[name$="[account_code]"]');
							const dIn = row.querySelector('input[name$="[debit_amount]"]');
							const cIn = row.querySelector('input[name$="[credit_amount]"]');
							const cashCat = row.querySelector('select[name$="[cash_category]"]');

							// Remove empty rows
							const accVal = acc ? acc.value.trim() : '';
							const dVal = dIn ? parseFloat(dIn.value) || 0 : 0;
							const cVal = cIn ? parseFloat(cIn.value) || 0 : 0;
							const keep = accVal !== '' || dVal !== 0 || cVal !== 0;

							if (!keep) {
								row.remove();
								return;
							}

							if (acc) acc.name = `lines[${idx}][account_id]`;
							if (codeIn) codeIn.name = `lines[${idx}][account_code]`;
							if (dIn) dIn.name = `lines[${idx}][debit_amount]`;
							if (cIn) cIn.name = `lines[${idx}][credit_amount]`;
							if (cashCat) cashCat.name = `lines[${idx}][cash_category]`;

							idx++;
						});
					}

			// Input events
			linesBody.addEventListener('input', e => {
				if (e.target.classList.contains('line-amount')) {
					recalcTotals();
				}
			});

			linesBody.addEventListener('click', e => {
				if (e.target.classList.contains('removeLine')) {
					e.target.closest('tr').remove();
					recalcTotals();
				}
			});

			addLineBtn.addEventListener('click', () => addRow());

			// Initial rows
			if (linesBody.querySelectorAll('tr').length === 0) {
				addRow();
				addRow();
			} else {
				recalcTotals();
			}

			form.addEventListener('submit', e => {
				normalizeRows();
				recalcTotals();
				const rows = linesBody.querySelectorAll('tr').length;
				const debit = parseFloat(totalDebitEl.textContent);
				const credit = parseFloat(totalCreditEl.textContent);

				if (rows < 2) {
					e.preventDefault();
					alert('Please add at least two lines.');
				}
				// if (Math.abs(debit - credit) > 0.005) {
				// 	e.preventDefault();
				// 	alert('Debits and credits must balance.');
				// }
			});

			document.addEventListener('change', function(e) {
				if (e.target.classList.contains('account-select')) {
					const row = e.target.closest('tr');
					const selectedOption = e.target.selectedOptions[0];
					const accountCode = selectedOption.dataset.code || '';

					// Set account code
					const codeInput = row.querySelector('.code-input');
					if (codeInput) codeInput.value = accountCode;
				}
			});

			document.addEventListener('DOMContentLoaded', function() {

				// Target all inputs with the line-amount class
				document.addEventListener('focusout', function(e) {
					if (e.target.classList.contains('line-amount')) {
						let val = parseFloat(e.target.value);

						// If the field is empty or NaN, reset to 0.00
						if (isNaN(val)) {
							e.target.value = "0.00";
						} else {
							// This forces the browser to show 100.00 instead of 100
							e.target.value = val.toFixed(2);
						}
					}
				});

				// UX Improvement: Select all text on click so user can replace 0.00 easily
				document.addEventListener('focusin', function(e) {
					if (e.target.classList.contains('line-amount')) {
						setTimeout(() => e.target.select(), 10);
					}
				});
			});


			function updateTotals() {
				let totalDebit = 0;
				let totalCredit = 0;

				// 1. Sum up all Debit inputs
				document.querySelectorAll('.debit').forEach(el => {
					totalDebit += parseFloat(el.value) || 0;
				});

				// 2. Sum up all Credit inputs
				document.querySelectorAll('.credit').forEach(el => {
					totalCredit += parseFloat(el.value) || 0;
				});

				// 3. Helper to format numbers with commas and 2 decimals
				const formatter = new Intl.NumberFormat('en-US', {
					minimumFractionDigits: 2,
					maximumFractionDigits: 2,
				});



				// 4. Update the <td> text content
				document.getElementById('totalDebit').textContent = formatter.format(totalDebit);
				document.getElementById('totalCredit').textContent = formatter.format(totalCredit);

				// 5. Visual Cue: Turn red if they don't match
				const diff = Math.abs(totalDebit - totalCredit);
				if (diff > 0.01) {
					document.getElementById('totalDebit').style.color = 'red';
					document.getElementById('totalCredit').style.color = 'red';
				} else {
					document.getElementById('totalDebit').style.color = 'green';
					document.getElementById('totalCredit').style.color = 'green';
				}
			}

			document.addEventListener('input', function(e) {
				if (e.target.classList.contains('line-amount')) {
					updateTotals();
				}
			});

			const searchInput = document.getElementById('searchInput');

			searchInput.addEventListener('input', function() {
				const filter = searchInput.value.toLowerCase();
				const rows = document.querySelectorAll('.entry-row');

				// We will put the rest of our logic here...

				// Inside the 'input' event listener:
				rows.forEach(row => {
					// We target the specific cells (columns)
					const dateText = row.cells[0].textContent.toLowerCase();
					const refText = row.cells[1].textContent.toLowerCase();
					const codeText = row.cells[2].textContent.toLowerCase();

					// Combine them to check all at once
					const combinedText = dateText + ' ' + refText + ' ' + codeText;

					if (combinedText.includes(filter)) {
						row.style.display = ""; // Show
					} else {
						row.style.display = "none"; // Hide
					}
				});
			});

				// Delete modal handling
				let deleteModal = document.getElementById('deleteModal');
				let deleteConfirmBtn = document.getElementById('deleteConfirm');
				let deleteCancelBtn = document.getElementById('deleteCancel');
				let deleteOverlay = document.getElementById('deleteModalOverlay');
				let pendingDeleteForm = null;

				document.addEventListener('click', function(e) {
					if (e.target.classList.contains('deleteBtn')) {
						e.preventDefault();
						const form = e.target.closest('form.delete-entry-form');
						if (!form) return;
						pendingDeleteForm = form;
						showDeleteModal();
					}
				});

				function showDeleteModal() {
					deleteModal.classList.remove('hidden');
					deleteModal.classList.add('flex');
				}

				function hideDeleteModal() {
					deleteModal.classList.add('hidden');
					deleteModal.classList.remove('flex');
					pendingDeleteForm = null;
				}

				deleteCancelBtn.addEventListener('click', hideDeleteModal);
				deleteOverlay.addEventListener('click', hideDeleteModal);

				deleteConfirmBtn.addEventListener('click', function() {
					if (pendingDeleteForm) pendingDeleteForm.submit();
					hideDeleteModal();
				});
		})();
	</script>
</x-app-layout>