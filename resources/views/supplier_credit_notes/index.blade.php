@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Supplier Credit Notes</h1>
            <p class="text-sm text-gray-600">Record credit notes issued by suppliers.</p>
        </div>
        <a href="#new-credit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm shadow hover:bg-blue-700">
            New Credit Note
        </a>
    </div>

    <!-- Search -->
    <div class="mb-4 flex gap-3">
        <form method="GET" class="flex-1">
            <input name="q"
                   value="{{ request('q') }}"
                   placeholder="Search credit notes..."
                   class="w-full p-2 bg-white border border-gray-300 rounded shadow-sm text-gray-900" />
        </form>

        <a href="{{ route('suppliercreditnotes.index') }}"
           class="px-3 py-2 bg-blue-100 border border-blue-300 text-blue-700 rounded text-sm hover:bg-blue-200">
            Refresh
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <div class="overflow-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-700 text-xs uppercase border-b">
                    <tr>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Reference</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Amount</th>
                        <th class="py-2">Applied</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($creditNotes ?? [] as $c)
                        <tr class="border-b hover:bg-gray-50 text-gray-900">
                            <td class="py-3">{{ $c->supplier->supplier_name ?? '—' }}</td>
                            <td class="py-3">{{ $c->credit_note_number ?? $c->reference }}</td>
                            <td class="py-3">{{ $c->credit_date ?? $c->entry_date }}</td>
                            <td class="py-3">{{ number_format($c->total_amount ?? $c->amount,2) }}</td>
                            <td class="py-3">{{ number_format($c->applied_amount ?? 0,2) }}</td>

                            <td class="py-3 flex gap-2">
                                <a href="{{ route('suppliercreditnotes.show', $c->id) }}"
                                   class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200">
                                    View
                                </a>
                                <a href="{{ route('suppliercreditnotes.edit', $c->id) }}"
                                   class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200">
                                    Edit
                                </a>

                                <form action="{{ route('suppliercreditnotes.destroy', $c->id) }}"
                                      method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200"
                                            onclick="return confirm('Delete this credit note?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-600">
                                No supplier credit notes yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <div class="mt-4">{{ $creditNotes->appends(request()->query())->links() }}</div>

    <!-- Create Form -->
    <div id="new-credit" class="mt-6 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">

        <h3 class="text-lg font-semibold text-gray-900 mb-3">Create Credit Note</h3>

        <form method="POST" action="{{ route('suppliercreditnotes.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-3">

                <!-- Supplier -->
                <div>
                    <label class="text-xs text-gray-700">Supplier</label>
                    <select name="supplier_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                            required>
                        <option value="">Select supplier</option>
                        @foreach($suppliers ?? [] as $s)
                            <option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Credit Note # -->
                <div>
                    <label class="text-xs text-gray-700">Credit Note Number</label>
                    <input name="credit_note_number"
                           class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                           placeholder="Credit note number"
                           required />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Date</label>
                    <input
                        type="text"
                        id="credit_date"
                        name="credit_date"
                        placeholder="Select date"
                        class="cursor-pointer mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                        required
                    />
                </div>


                <!-- Amount -->
                <div>
                    <label class="text-xs text-gray-700">Amount</label>
                    <input name="total_amount"
                           class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                           required />
                </div>

                <!-- AP Account -->
                <div>
                    <label class="text-xs text-gray-700">AP Account</label>
                    <select name="ap_account_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                            required>
                        <option value="">AP account</option>
                        @foreach($accounts ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Offset Account -->
                <div>
                    <label class="text-xs text-gray-700">Offset Account</label>
                    <select name="offset_account_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                            required>
                        <option value="">Offset account</option>
                        @foreach($accounts ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reason -->
                <div class="col-span-2">
                    <label class="text-xs text-gray-700">Reason (optional)</label>
                    <input name="reason"
                           placeholder="Reason"
                           class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900" />
                </div>

                <div class="col-span-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                        Create Credit Note
                    </button>
                </div>

            </div>
        </form>

    </div>

</div>

<script>
flatpickr("#credit_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    defaultDate: "{{ old('credit_date') }}"
});
</script>

@endsection
