<x-app-layout>
	<div class="py-6">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="flex items-center justify-between mb-6">
				<div>
					<h1 class="text-2xl font-bold text-gray-900">Accounting Periods</h1>
					<p class="text-sm text-gray-600 mt-1">Manage your fiscal periods and year-end closures</p>
				</div>
			</div>

			@if(session('success'))
				<div class="mb-4 p-3 bg-green-100 border border-green-300 rounded text-green-800">{{ session('success') }}</div>
			@endif

			@if($errors->any())
				<div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
					<ul class="list-disc pl-5">
						@foreach($errors->all() as $err)
							<li>{{ $err }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			{{-- Create form --}}
			<div class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
				<h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Period</h2>
				<form method="POST" action="{{ route('accountingperiod.store') ?? '#' }}">
					@csrf
					<div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
						<div>
							<label class="block text-sm font-medium text-gray-700">Period name</label>
							<input type="text" name="period_name" value="{{ old('period_name') }}" class="mt-1 block w-full bg-white border-gray-300 text-gray-900 rounded px-3 py-2">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700">Start date</label>
							<input type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 block w-full bg-white border-gray-300 text-gray-900 rounded px-3 py-2">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700">End date</label>
							<input type="date" name="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full bg-white border-gray-300 text-gray-900 rounded px-3 py-2">
						</div>
					</div>
					<div class="mt-4 flex gap-3">
						<button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Create Period</button>
						<button type="button" onclick="this.form.reset()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded">Reset</button>
					</div>
				</form>
			</div>

			{{-- Periods table --}}
			<div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-4 py-3 text-left text-gray-900 font-semibold">Name</th>
							<th class="px-4 py-3 text-left text-gray-900 font-semibold">Start Date</th>
							<th class="px-4 py-3 text-left text-gray-900 font-semibold">End Date</th>
							<th class="px-4 py-3 text-left text-gray-900 font-semibold">Closed</th>
							<th class="px-4 py-3 text-right text-gray-900 font-semibold">Actions</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						@if(isset($periods) && $periods->count())
							@foreach($periods as $p)
								<tr class="hover:bg-gray-50">
									<td class="px-4 py-3 text-gray-900">{{ $p->period_name }}</td>
									<td class="px-4 py-3 text-gray-900">{{ optional($p->start_date)->toDateString() }}</td>
									<td class="px-4 py-3 text-gray-900">{{ optional($p->end_date)->toDateString() }}</td>
									<td class="px-4 py-3">
										@if($p->is_closed)
											<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded border border-red-300">Closed</span>
										@else
											<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded border border-green-300">Open</span>
										@endif
									</td>
									<td class="px-4 py-3 text-right">
										<a href="{{ route('accountingperiod.edit', $p->id) ?? '#' }}" class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200 rounded text-yellow-800 border border-yellow-300">Edit</a>
									</td>
								</tr>
							@endforeach
						@else
							<tr>
								<td class="px-4 py-3 text-center text-gray-600" colspan="5">No accounting periods found.</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</x-app-layout>