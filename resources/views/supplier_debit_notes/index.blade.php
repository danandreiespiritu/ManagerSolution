@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Supplier Debit Notes</h1>
            <p class="text-sm text-gray-600">Record debit notes relating to suppliers.</p>
        </div>
        <a href="#new-debit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm shadow hover:bg-blue-700">
            New Debit Note
        </a>
    </div>

    <!-- Search -->
    <div class="mb-4 flex gap-3">
        <form method="GET" class="flex-1">
            <input name="q"
                   value="{{ request('q') }}"
                   placeholder="Search debit notes..."
                   class="w-full p-2 bg-white border border-gray-300 rounded shadow-sm text-gray-900" />
        </form>

        <a href="{{ route('supplierdebitnotes.index') }}"
           class="px-3 py-2 bg-blue-100 text-blue-700 border border-blue-300 rounded text-sm hover:bg-blue-200">
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
                        <th class="py-2">Status</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($debitNotes ?? [] as $d)
                        <tr class="border-b hover:bg-gray-50 text-gray-900">
                            <td class="py-3">{{ $d->supplier->supplier_name ?? '—' }}</td>
                            <td class="py-3">{{ $d->debit_note_number ?? $d->reference }}</td>
                            <td class="py-3">{{ $d->debit_date ?? $d->entry_date }}</td>
                            <td class="py-3">{{ number_format($d->total_amount ?? $d->amount,2) }}</td>
                            <td class="py-3">{{ $d->status ?? 'Open' }}</td>

                            <td class="py-3 flex gap-2">
                                <a href="{{ route('supplierdebitnotes.show', $d->id) }}"
                                   class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200">
                                    View
                                </a>

                                <a href="{{ route('supplierdebitnotes.edit', $d->id) }}"
                                   class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200">
                                    Edit
                                </a>

                                <form action="{{ route('supplierdebitnotes.destroy', $d->id) }}"
                                      method="POST"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')

                                    <button class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200"
                                            onclick="return confirm('Delete this debit note?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-600">
                                No supplier debit notes yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <div class="mt-4">{{ $debitNotes->appends(request()->query())->links() }}</div>

    <!-- Create New Debit Note -->
    <div id="new-debit" class="mt-6 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Create Debit Note</h3>

        <form method="POST" action="{{ route('supplierdebitnotes.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-3">

                <div>
                    <label class="text-xs text-gray-700">Supplier</label>
                    <select name="supplier_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded text-gray-900 shadow-sm"
                            required>
                        <option value="">Select supplier</option>
                        @foreach($suppliers ?? [] as $s)
                            <option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-700">Debit Note Number</label>
                    <input name="debit_note_number"
                           placeholder="Debit note number"
                           class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm"
                           required />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Date</label>
                    <input
                        type="text"
                        id="debit_date"
                        name="debit_date"
                        placeholder="Select date"
                        class="cursor-pointer mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm"
                        required
                    />
                </div>


                <div>
                    <label class="text-xs text-gray-700">Amount</label>
                    <input name="total_amount"
                           class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm"
                           required />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Expense Account</label>
                    <select name="expense_account_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                            required>
                        <option value="">Expense account</option>
                        @foreach($accounts ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

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

                <div class="col-span-2">
                    <label class="text-xs text-gray-700">Reason (optional)</label>
                    <input name="reason"
                           placeholder="Reason"
                           class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm" />
                </div>

                <div class="col-span-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                        Create Debit Note
                    </button>
                </div>

            </div>
        </form>

    </div>

</div>

<script>
flatpickr("#debit_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true,
    defaultDate: "{{ old('debit_date') }}"
});
</script>

@endsection
