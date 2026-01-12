<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Customer Credit Notes</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage credit notes for customers</p>
                </div>
                <a href="#new-credit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">New Credit Note</a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">{{ session('error') }}</div>
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

            <div class="bg-white rounded shadow border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Customer</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Number</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Date</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Amount</th>
                            <th class="px-4 py-3 text-right text-gray-900 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($creditNotes as $n)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $n->customer?->customer_name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $n->credit_note_number }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $n->credit_date?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ number_format($n->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('customercreditnotes.show', $n->id) }}" class="inline-block px-3 py-1 bg-blue-100 hover:bg-blue-200 border border-blue-300 text-blue-800 rounded mr-2">View</a>
                                    <a href="{{ route('customercreditnotes.edit', $n->id) }}" class="inline-block px-3 py-1 bg-yellow-100 hover:bg-yellow-200 border border-yellow-300 text-yellow-800 rounded mr-2">Edit</a>
                                    <form action="{{ route('customercreditnotes.destroy', $n->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete credit note?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 rounded">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-600">No credit notes found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $creditNotes->links() }}
            </div>

            <div id="new-credit" class="mt-8 bg-white border border-gray-200 p-6 rounded shadow">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Create Credit Note</h2>
                <form method="POST" action="{{ route('customercreditnotes.store') }}">
                    @csrf

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Customer</label>
                            <select name="customer_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option class="text-black" value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Credit Note Number</label>
                            <input type="text" name="credit_note_number" value="{{ old('credit_note_number') }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Date</label>
                            <input type="date" name="credit_date" value="{{ old('credit_date', now()->toDateString()) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Amount</label>
                            <input type="text" name="total_amount" value="{{ old('total_amount') }}" class="w-full border rounded px-3 py-2 text-white" placeholder="0.00">
                        </div>
                        <div class="col-span-12">
                            <label class="block text-sm text-white">Reason (optional)</label>
                            <textarea name="reason" class="w-full border rounded px-3 py-2 text-white">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
