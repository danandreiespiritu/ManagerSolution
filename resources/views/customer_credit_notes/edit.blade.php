<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Edit Credit Note</h2>
    </x-slot>

    <div class="py-6 text-white">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 p-6 rounded">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-600 text-white rounded">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-600 text-white rounded">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customercreditnotes.update', $note->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Customer</label>
                            <select name="customer_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option class="text-black" value="{{ $c->id }}" @selected(old('customer_id', $note->customer_id) == $c->id)>{{ $c->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Credit Note Number</label>
                            <input type="text" name="credit_note_number" value="{{ old('credit_note_number', $note->credit_note_number) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Date</label>
                            <input type="date" name="credit_date" value="{{ old('credit_date', $note->credit_date?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-6">
                            <label class="block text-sm text-white">Amount</label>
                            <input type="text" name="total_amount" value="{{ old('total_amount', $note->total_amount) }}" class="w-full border rounded px-3 py-2 text-white" placeholder="0.00">
                        </div>
                        <div class="col-span-12">
                            <label class="block text-sm text-white">Reason (optional)</label>
                            <textarea name="reason" class="w-full border rounded px-3 py-2 text-white">{{ old('reason', $note->reason) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('customercreditnotes.show', $note->id) }}" class="px-4 py-2 border rounded">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
