<x-app-layout>
	<div class="py-8">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

			<!-- Page Header -->
			<div class="flex items-center justify-between mb-6">
				<div>
					<h1 class="text-3xl font-bold text-gray-900">Accounting Periods</h1>
					<p class="text-gray-600 mt-1 text-sm">Manage fiscal periods, opening balances, and year-end closures</p>
				</div>
			</div>

			<!-- Success Message -->
			@if(session('success'))
				<div class="mb-4 bg-green-50 text-green-700 border border-green-300 p-3 rounded-md shadow-sm">
					{{ session('success') }}
				</div>
			@endif

			<!-- Validation Errors -->
			@if($errors->any())
				<div class="mb-4 bg-red-50 text-red-700 border border-red-300 p-4 rounded-md shadow-sm">
					<ul class="list-disc ml-6 text-sm">
						@foreach($errors->all() as $err)
							<li>{{ $err }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<!-- Create New Period Card -->
			<div class="bg-white rounded-xl shadow border border-gray-200 p-6 mb-8">
				<h2 class="text-xl font-semibold text-gray-900 mb-4">Create New Period</h2>

				<form method="POST" action="{{ route('accountingperiod.store') }}">
					@csrf

					<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

						<!-- Period Name -->
						<div>
							<label class="block text-sm font-medium text-gray-700">Period Name</label>
							<input type="text" name="period_name" value="{{ old('period_name') }}"
								class="mt-1 block w-full px-3 py-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
						</div>

						<!-- Start Date -->
						<div>
							<label class="block text-sm font-medium text-gray-700">Start Date</label>
							<input type="date" name="start_date" value="{{ old('start_date') }}"
								class="mt-1 block w-full px-3 py-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
						</div>

						<!-- End Date -->
						<div>
							<label class="block text-sm font-medium text-gray-700">End Date</label>
							<input type="date" name="end_date" value="{{ old('end_date') }}"
								class="mt-1 block w-full px-3 py-2 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
						</div>
					</div>

					<!-- Form Buttons -->
					<div class="mt-6 flex gap-3">
						<button type="submit"
							class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
							Create Period
						</button>

						<button type="button" onclick="this.form.reset()"
							class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow">
							Reset
						</button>
					</div>

				</form>
			</div>

			<!-- Periods Listing -->
			<div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">

				<table class="min-w-full divide-y divide-gray-200 text-sm">
					<thead class="bg-gray-100 sticky top-0">
						<tr>
							<th class="px-4 py-3 text-left font-semibold text-gray-800">Name</th>
							<th class="px-4 py-3 text-left font-semibold text-gray-800">Start Date</th>
							<th class="px-4 py-3 text-left font-semibold text-gray-800">End Date</th>
							<th class="px-4 py-3 text-left font-semibold text-gray-800">Status</th>
						</tr>
					</thead>

					<tbody class="divide-y divide-gray-200 bg-white">
						@if(isset($periods) && $periods->count())
							@foreach($periods as $p)
								<tr class="hover:bg-gray-50 transition">
									<td class="px-4 py-3 text-gray-900">{{ $p->period_name }}</td>
									<td class="px-4 py-3 text-gray-700">{{ optional($p->start_date)->format('Y-m-d') }}</td>
									<td class="px-4 py-3 text-gray-700">{{ optional($p->end_date)->format('Y-m-d') }}</td>

									<!-- Closed Status Badge -->
									<td class="px-4 py-3">
										@if($p->is_closed)
											<span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-lg border border-red-300">
												Closed
											</span>
										@else
											<span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-lg border border-green-300">
												Open
											</span>
										@endif
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td colspan="5" class="px-4 py-6 text-center text-gray-500">
									No accounting periods found.
								</td>
							</tr>
						@endif
					</tbody>
				</table>

			</div>

		</div>
	</div>
</x-app-layout>
