@props(['businesses' => null])

<div class="rounded-lg border border-gray-200 overflow-hidden bg-white shadow-sm">
	<div class="px-4 py-4">
		<table class="w-full">
			<thead>
				<tr class="text-left text-sm text-gray-500 uppercase tracking-wider">
					<th class="pl-4 py-3">Name</th>
					<th class="py-3 text-right pr-4">Actions</th>
				</tr>
			</thead>
			<tbody class="bg-white">
				@forelse($businesses ?? [] as $business)
					@php $bizKey = $business->id ?? $business->uuid ?? null; @endphp
					<tr class="border-t border-gray-100">
							<td class="pl-4 py-5">
								<a href="{{ route('business.summary', $bizKey) }}" class="text-indigo-600 hover:underline text-base">{{ $business->business_name }}</a>
							</td>
						<td class="py-5 text-right pr-4">
							<div class="inline-flex items-center gap-3">
								@if($bizKey)
									<a href="{{ route('business.edit', $bizKey) }}" class="text-sm text-gray-600 hover:text-gray-900">Edit</a>
									<form action="{{ route('business.destroy', $bizKey) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this business?');" class="inline-block">
										@csrf
										@method('DELETE')
										<button type="submit" class="ml-2 inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md text-sm">Delete</button>
									</form>
								@endif
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="2" class="px-4 py-6 text-sm text-gray-500">No businesses found.</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if(isset($businesses) && is_object($businesses) && method_exists($businesses, 'links'))
		<div class="px-4 py-3 bg-white border-t border-gray-100">
			{{ $businesses->links() }}
		</div>
	@endif
</div>