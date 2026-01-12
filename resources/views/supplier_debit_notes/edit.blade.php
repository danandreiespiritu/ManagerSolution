@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <h1 class="text-xl font-semibold text-gray-900">Edit Supplier Debit Note</h1>

    <form method="POST"
          action="{{ route('supplierdebitnotes.update', $note->id) }}"
          class="mt-4 bg-white p-4 border border-gray-200 rounded shadow-sm">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-3">

            <div>
                <label class="text-xs text-gray-700">Debit Note Number</label>
                <input name="debit_note_number"
                       value="{{ $note->debit_note_number }}"
                       class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm"
                       required />
            </div>

            <div>
                <label class="text-xs text-gray-700">Supplier</label>
                <select name="supplier_id"
                        class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm">
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $note->supplier_id ? 'selected' : '' }}>
                            {{ $s->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">Date</label>
                <input type="date"
                       name="debit_date"
                       value="{{ $note->debit_date->format('Y-m-d') }}"
                       class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm" />
            </div>

            <div>
                <label class="text-xs text-gray-700">Amount</label>
                <input type="number"
                       step="0.01"
                       min="0.01"
                       name="total_amount"
                       value="{{ $note->total_amount }}"
                       class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm"
                       required />
            </div>

            <div>
                <label class="text-xs text-gray-700">Expense Account</label>
                <select name="expense_account_id"
                        class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm">
                    @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ $a->id == $note->expense_account_id ? 'selected' : '' }}>
                            {{ $a->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">AP Account</label>
                <select name="ap_account_id"
                        class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm">
                    @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ $a->id == $note->ap_account_id ? 'selected' : '' }}>
                            {{ $a->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2">
                <label class="text-xs text-gray-700">Reason</label>
                <input name="reason"
                       value="{{ $note->reason }}"
                       class="mt-1 p-2 w-full border border-gray-300 bg-white text-gray-900 rounded shadow-sm" />
            </div>
        </div>

        <div class="mt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                Save
            </button>

            <a href="{{ route('supplierdebitnotes.index') }}"
               class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Cancel
            </a>
        </div>

    </form>

</div>
@endsection
