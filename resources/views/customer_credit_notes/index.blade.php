<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Customer Credit Notes</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage and create credit notes for customers</p>
                </div>
                <a href="#new-credit"
                   class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow
                          hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    New Credit Note
                </a>
            </div>

            <!-- ALERTS -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg shadow-sm">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- TABLE -->
            <div class="bg-white shadow border border-gray-200 rounded-xl overflow-hidden">

                <table class="w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Number</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Amount</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($creditNotes as $n)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ $n->customer?->customer_name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $n->credit_note_number }}</td>
                                <td class="px-4 py-3">{{ optional($n->credit_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ number_format($n->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('customercreditnotes.show', $n->id) }}"
                                       class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 border border-blue-300 rounded-md text-xs hover:bg-blue-200 mr-2">
                                        View
                                    </a>
                                    <a href="{{ route('customercreditnotes.edit', $n->id) }}"
                                       class="inline-block px-3 py-1.5 bg-yellow-100 text-yellow-700 border border-yellow-300 rounded-md text-xs hover:bg-yellow-200 mr-2">
                                        Edit
                                    </a>
                                    <form action="{{ route('customercreditnotes.destroy', $n->id) }}"
                                          method="POST" class="inline-block"
                                          onsubmit="return confirm('Delete this credit note?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-md hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">
                                    No credit notes found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $creditNotes->links() }}
            </div>

            <!-- CREATE FORM -->
            <div id="new-credit" class="mt-10 bg-white border border-gray-200 rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Create Credit Note</h2>

                <form method="POST" action="{{ route('customercreditnotes.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Customer -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Customer</label>
                            <select name="customer_id"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Credit Note Number -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Credit Note Number</label>
                            <input type="text" name="credit_note_number"
                                   value="{{ old('credit_note_number') }}"
                                   class="w-full px-3 py-2 text-sm border rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Date</label>
                            <input type="date" name="credit_date"
                                   value="{{ old('credit_date', now()->toDateString()) }}"
                                   class="w-full px-3 py-2 text-sm border rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm text-gray-700 font-medium mb-1">Amount</label>
                            <input type="text" name="total_amount" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   value="{{ old('total_amount') }}"
                                   placeholder="0.00"
                                   class="w-full px-3 py-2 text-sm border rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm text-gray-700 font-medium mb-1">Reason (optional)</label>
                        <textarea name="reason" rows="4"
                                  class="w-full px-3 py-2 text-sm border rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                    </div>

                    <!-- Save Button -->
                    <div>
                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow hover:bg-indigo-700 focus:ring-indigo-500">
                            Save Credit Note
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
