@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <h1 class="text-xl font-semibold text-gray-900">Edit Supplier Bill</h1>

    <form method="POST" action="{{ route('supplierbills.update', $bill->id) }}"
          class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-3">

            <div>
                <label class="text-xs text-gray-700">Bill Number</label>
                <input name="bill_number"
                       value="{{ $bill->bill_number }}"
                       class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm text-gray-900" required />
            </div>

            <div>
                <label class="text-xs text-gray-700">Supplier</label>
                <select name="supplier_id"
                        class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm text-gray-900">
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $bill->supplier_id ? 'selected' : '' }}>
                            {{ $s->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">Bill Date</label>
                <input name="bill_date" type="date"
                       value="{{ $bill->bill_date->format('Y-m-d') }}"
                       class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm text-gray-900" required />
            </div>

            <div>
                <label class="text-xs text-gray-700">Due Date</label>
                <input name="due_date" type="date"
                       value="{{ optional($bill->due_date)->format('Y-m-d') }}"
                       class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm text-gray-900" />
            </div>

            <div>
                <label class="text-xs text-gray-700">Amount</label>
                <input name="total_amount"
                       value="{{ $bill->total_amount }}"
                       class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm text-gray-900"
                       type="number" step="0.01" min="0.01" required />
            </div>

        </div>

        <div class="mt-3 grid grid-cols-2 gap-3">
            <select name="expense_account_id"
                    class="p-2 border border-gray-300 rounded shadow-sm text-gray-900">
                @foreach($accounts as $a)
                    <option value="{{ $a->id }}" {{ $a->id == $bill->expense_account_id ? 'selected' : '' }}>
                        {{ $a->account_name }}
                    </option>
                @endforeach
            </select>

            <select name="ap_account_id"
                    class="p-2 border border-gray-300 rounded shadow-sm text-gray-900">
                @foreach($accounts as $a)
                    <option value="{{ $a->id }}" {{ $a->id == $bill->ap_account_id ? 'selected' : '' }}>
                        {{ $a->account_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Save</button>
            <a href="{{ route('supplierbills.index') }}"
               class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</a>
        </div>

    </form>

</div>
@endsection
