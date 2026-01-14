@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Supplier Bills</h1>
            <p class="text-sm text-gray-600">Record and view supplier bills.</p>
        </div>
       
    </div>

    <div class="mb-4 flex gap-3">
        <form method="GET" class="flex-1">
            <input name="q"
                   value="{{ request('q') }}"
                   placeholder="Search bills..."
                   class="w-full p-2 bg-white border border-gray-300 rounded shadow-sm text-gray-900" />
        </form>

        <a href="{{ route('supplierbills.index') }}"
           class="px-3 py-2 bg-blue-100 text-blue-600 border border-blue-300 rounded text-sm hover:bg-blue-200">
            Refresh
        </a>
    </div>

    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 border border-green-100 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 text-red-700 border border-red-100 rounded">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 bg-yellow-50 text-yellow-800 border border-yellow-100 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="overflow-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-700 text-xs uppercase border-b">
                    <tr>
                        <th class="py-2">Supplier</th>
                        <th class="py-2">Reference</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Amount</th>
                        <th class="py-2">Applied</th>
                        <th class="py-2">Balance</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bills ?? [] as $bill)
                        <tr class="border-b hover:bg-gray-50 text-gray-900">
                            <td class="py-3">{{ $bill->supplier->supplier_name ?? '—' }}</td>
                            <td class="py-3">{{ $bill->bill_number ?? $bill->reference }}</td>
                            <td class="py-3">{{ $bill->bill_date ?? $bill->entry_date }}</td>
                            <td class="py-3">{{ number_format($bill->total_amount ?? $bill->amount,2) }}</td>
                            <td class="py-3">{{ number_format($bill->appliedAmount(),2) }}</td>
                            <td class="py-3">{{ number_format($bill->balanceDue(),2) }}</td>
                            <td class="py-3">{{ $bill->status ?? 'Open' }}</td>

                            <td class="py-3">
                                <a href="{{ route('supplierbills.show', $bill->id) }}"
                                   class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs hover:bg-blue-200">
                                    View
                                </a>

                                <a href="{{ route('supplierbills.edit', $bill->id) }}"
                                   class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200">
                                    Edit
                                </a>

                                <form action="{{ route('supplierbills.destroy', $bill->id) }}"
                                      method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')

                                    <button class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200"
                                            onclick="return confirm('Delete this bill?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-600">
                                No supplier bills found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $bills->appends(request()->query())->links() }}</div>

    <!-- Create Bill Form -->
    <div id="new-bill"
         class="mt-6 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Create Supplier Bill</h3>

        <form method="POST" action="{{ route('supplierbills.store') }}">
            @csrf

            <div>
                <label class="text-xs text-gray-700">Supplier</label>
                <select name="supplier_id"
                        class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                        required>
                    <option value="">Select supplier</option>
                    @foreach($suppliers ?? [] as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">

                <div>
                    <label class="text-xs text-gray-700">Bill Number</label>
                          <input name="bill_number"
                              value="{{ old('bill_number') }}"
                              class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                              required />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Bill Date</label>
                    <input name="bill_date" id="bill_date"
                        class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900 cursor-pointer"
                        type="date" placeholder="Select date" value="{{ old('bill_date') }}" />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Due Date (optional)</label>
                    <input name="due_date" id="due_date"
                        class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900 cursor-pointer"
                        type="date" placeholder="Select date" value="{{ old('due_date') }}" />
                </div>


                <div>
                    <label class="text-xs text-gray-700">Total Amount</label>
                          <input name="total_amount"
                              value="{{ old('total_amount') }}"
                              class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                              required />
                </div>

                <div>
                    <label class="text-xs text-gray-700">Expense Account</label>
                    <select name="expense_account_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                            required>
                        <option value="">Select account</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}" {{ old('expense_account_id') == $a->id ? 'selected' : '' }}>{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-700">AP Account</label>
                    <select name="ap_account_id"
                            class="mt-1 p-2 w-full bg-white border border-gray-300 rounded shadow-sm text-gray-900"
                            required>
                        <option value="">Select account</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}" {{ old('ap_account_id') == $a->id ? 'selected' : '' }}>{{ $a->account_name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                Create Bill
            </button>

        </form>
    </div>

</div>
<script>
flatpickr("#bill_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true
});

flatpickr("#due_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    allowInput: true
});
</script>

@endsection
