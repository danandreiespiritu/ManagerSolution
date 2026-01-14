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
							class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
							value="{{ old('entry_date', isset($entry) ? $entry->entry_date->format('Y-m-d') : date('Y-m-d')) }}">
					</div>

					<!-- Accounting Period -->
					<div>
						<label class="block text-sm font-medium text-gray-700">Accounting Period</label>
						<select name="accounting_period_id"
							class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
							<option value="">-- Select period --</option>
							@foreach($periods as $p)
								<option value="{{ $p->id }}"
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
								class="mt-1 block w-1/2 border-gray-300 rounded-lg shadow-sm">
							<input type="text" name="reference_id" placeholder="ID"
								value="{{ old('reference_id', $entry->reference_id ?? '') }}"
								class="mt-1 block w-1/2 border-gray-300 rounded-lg shadow-sm">
						</div>
					</div>
				</div>

				<!-- DESCRIPTION -->
				<div class="mb-6">
					<label class="block text-sm font-medium text-gray-700">Description</label>
					<textarea name="description" rows="2"
						class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $entry->description ?? '') }}</textarea>
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

					<div class="overflow-auto max-h-72 border border-gray-200 rounded-lg">
						<table class="min-w-full text-sm" id="linesTable">
							<thead class="bg-gray-100 sticky top-0 z-10">
								<tr>
									<th class="px-3 py-2 text-left">Account</th>
									<th class="px-3 py-2 text-left">Description</th>
									<th class="px-3 py-2 text-left">Customer</th>
									<th class="px-3 py-2 text-left">Supplier</th>
									<th class="px-3 py-2 text-right">Debit</th>
									<th class="px-3 py-2 text-right">Credit</th>
									<th class="px-3 py-2 text-center">Actions</th>
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
									<td colspan="4" class="px-3 py-2 text-right">Totals</td>
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
				<h3 class="text-xl font-semibold mb-3">Recent Journal Entries</h3>

				@if(isset($recentEntries) && $recentEntries->count())
					<div class="bg-white rounded-lg shadow overflow-hidden">

						<table class="min-w-full text-sm">
							<thead class="bg-gray-100">
								<tr>
									<th class="px-3 py-2 text-left">Date</th>
									<th class="px-3 py-2 text-left">Reference</th>
									<th class="px-3 py-2 text-left">Description</th>
									<th class="px-3 py-2 text-right">Debit</th>
									<th class="px-3 py-2 text-right">Credit</th>
									<th class="px-3 py-2 text-center">Actions</th>
								</tr>
							</thead>

							<tbody class="divide-y">
								@foreach($recentEntries as $entryRow)
									<tr class="hover:bg-gray-50">
										<td class="px-3 py-2">{{ $entryRow->entry_date->format('Y-m-d') }}</td>
										<td class="px-3 py-2">{{ $entryRow->reference_type }} {{ $entryRow->reference_id }}</td>
										<td class="px-3 py-2">{{ Str::limit($entryRow->description, 70) }}</td>
										<td class="px-3 py-2 text-right">{{ number_format($entryRow->lines->sum('debit_amount'),2) }}</td>
										<td class="px-3 py-2 text-right">{{ number_format($entryRow->lines->sum('credit_amount'),2) }}</td>
										<td class="px-3 py-2 text-center space-x-3">
											<a href="{{ route('journal.show', $entryRow->id) }}" class="text-blue-600">View</a>
											<a href="{{ route('journal.edit', $entryRow->id) }}" class="text-yellow-600">Edit</a>

											<form action="{{ route('journal.destroy', $entryRow->id) }}" method="POST"
												class="inline-block"
												onsubmit="return confirm('Delete this journal entry?');">
												@csrf @method('DELETE')
												<button class="text-red-600">Delete</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>

						</table>
					</div>
				@else
					<p class="text-gray-500">No recent entries found.</p>
				@endif

			</div>
		</div>
	</div>

	<!-- TEMPLATE ROW FOR JS -->
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
				<input type="text" name="lines[][description]" class="w-full border-gray-200 rounded" placeholder="Description">
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
				<input type="number" name="lines[][debit_amount]" value="0" step="0.01" min="0"
					class="w-32 text-right border-gray-200 rounded line-amount debit">
			</td>
			<td class="px-3 py-2 text-right">
				<input type="number" name="lines[][credit_amount]" value="0" step="0.01" min="0"
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

	<!-- JAVASCRIPT (unchanged except formatting) -->
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

				// Prefill values
				tr.querySelector('[name$="[account_id]"]').value = prefill.account_id ?? "";
				tr.querySelector('[name$="[description]"]').value = prefill.description ?? "";
				tr.querySelector('[name$="[customer_id]"]').value = prefill.customer_id ?? "";
				tr.querySelector('[name$="[supplier_id]"]').value = prefill.supplier_id ?? "";
				tr.querySelector('[name$="[debit_amount]"]').value = prefill.debit_amount ?? 0;
				tr.querySelector('[name$="[credit_amount]"]').value = prefill.credit_amount ?? 0;

				linesBody.appendChild(tr);
				recalcTotals();
			}

			function recalcTotals(){
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

				submitBtn.disabled = Math.abs(debit - credit) > 0.005;
				submitBtn.classList.toggle('opacity-50', submitBtn.disabled);
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
				const rows = linesBody.querySelectorAll('tr').length;
				const debit = parseFloat(totalDebitEl.textContent);
				const credit = parseFloat(totalCreditEl.textContent);

				if (rows < 2) {
					e.preventDefault();
					alert('Please add at least two lines.');
				}
				if (Math.abs(debit - credit) > 0.005) {
					e.preventDefault();
					alert('Debits and credits must balance.');
				}
			});
		})();
	</script>
</x-app-layout>
