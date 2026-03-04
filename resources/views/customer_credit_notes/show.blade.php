<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Credit Note #{{ $note->credit_note_number }}
                </h1>

                <div class="space-x-2">
                    <a href="{{ route('customercreditnotes.edit', $note->id) }}"
                        class="px-4 py-2 bg-yellow-100 text-yellow-800 border border-yellow-300 rounded-lg text-sm shadow hover:bg-yellow-200">
                        Edit
                    </a>

                    <a href="{{ route('customercreditnotes.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-sm shadow hover:bg-gray-300">
                        Back
                    </a>
                </div>
            </div>

            <!-- DETAILS CARD -->
            <div class="bg-white border border-gray-200 rounded-xl shadow p-6">

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Customer -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-base text-gray-900">
                            {{ $note->customer?->customer_name ?? '—' }}
                        </dd>
                    </div>

                    <!-- Date -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                        <dd class="mt-1 text-base text-gray-900">
                            {{ $note->credit_date?->format('Y-m-d') }}
                        </dd>
                    </div>

                    <!-- Amount -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-base font-semibold text-gray-900">
                            {{ number_format($note->total_amount, 2) }}
                        </dd>
                    </div>

                </dl>

                <!-- Reason -->
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Reason</h3>
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-800 text-sm">
                        {{ $note->reason ?: '—' }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end border-t pt-6">

                    <form action="{{ route('customercreditnotes.destroy', $note->id) }}"
                          method="POST"
                          onsubmit="return confirm('Delete this credit note?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="px-5 py-2.5 bg-red-600 text-white text-sm rounded-lg shadow hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-1">
                            Delete
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
