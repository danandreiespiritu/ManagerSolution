<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Credit Note {{ $note->credit_note_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Credit Note {{ $note->credit_note_number }}</h1>
                <div class="space-x-2">
                    <a href="{{ route('customercreditnotes.edit', $note->id) }}" class="px-3 py-1 bg-yellow-500 rounded">Edit</a>
                    <a href="{{ route('customercreditnotes.index') }}" class="px-3 py-1 bg-gray-700 rounded">Back</a>
                </div>
            </div>

            <div class="bg-[#111827] p-6 rounded">
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-400">Customer</dt>
                        <dd class="text-white">{{ $note->customer?->customer_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Date</dt>
                        <dd class="text-white">{{ $note->credit_date?->format('Y-m-d') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Amount</dt>
                        <dd class="text-white">{{ number_format($note->total_amount, 2) }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <h3 class="font-semibold mb-2 text-white">Reason</h3>
                    <div class="bg-gray-800 p-3 rounded text-white">{{ $note->reason ?? '—' }}</div>
                </div>

                <div class="mt-6 flex justify-end">
                    <form action="{{ route('customercreditnotes.destroy', $note->id) }}" method="POST" onsubmit="return confirm('Delete credit note?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 rounded text-white">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
