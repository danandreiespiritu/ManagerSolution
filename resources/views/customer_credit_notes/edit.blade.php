<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Credit Note</h1>

                <a href="{{ route('customercreditnotes.show', $note->id) }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow text-sm">
                    Back
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-300 text-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-300 text-red-800 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-300 text-red-800 shadow-sm">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Container -->
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

                <form method="POST" action="{{ route('customercreditnotes.update', $note->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Customer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <select name="customer_id"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-800 text-sm
                                       focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">

                                <option value="">-- Select customer --</option>

                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}"
                                        @selected(old('customer_id', $note->customer_id) == $c->id)>
                                        {{ $c->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Credit Note Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Credit Note Number</label>
                            <input type="text"
                                   name="credit_note_number"
                                   value="{{ old('credit_note_number', $note->credit_note_number) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                          focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date"
                                   name="credit_date"
                                   value="{{ old('credit_date', $note->credit_date?->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                          focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                            <input type="text"
                                   name="total_amount"
                                   value="{{ old('total_amount', $note->total_amount) }}"
                                   placeholder="0.00"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                          focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason (optional)</label>
                        <textarea name="reason"
                                  rows="4"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                         focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('reason', $note->reason) }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center border-t pt-6">

                        <a href="{{ route('customercreditnotes.show', $note->id) }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow text-sm">
                            Cancel
                        </a>

                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow text-sm font-medium
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Save Changes
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
