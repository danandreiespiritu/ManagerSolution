@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-xl font-semibold text-gray-900">Edit Supplier Payment</h1>

    <form method="POST" action="{{ route('supplierpayments.update', $payment->id) }}"
          class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-3">

            <div>
                <label class="text-xs text-gray-700">Supplier</label>
                <select name="supplier_id"
                        class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $s->id == $payment->supplier_id ? 'selected' : '' }}>
                            {{ $s->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">Payment Method</label>
                <select name="payment_method"
                        class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                    <option value="">Select method</option>
                    <option value="bank_transfer" {{ $payment->payment_method === 'bank_transfer' ? 'selected' : '' }}>Bank transfer</option>
                    <option value="cash" {{ $payment->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="cheque" {{ $payment->payment_method === 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="card" {{ $payment->payment_method === 'card' ? 'selected' : '' }}>Card</option>
                    <option value="eft" {{ $payment->payment_method === 'eft' ? 'selected' : '' }}>EFT</option>
                    <option value="other" {{ $payment->payment_method === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">Date</label>
                <input name="payment_date"
                       type="date"
                       value="{{ $payment->payment_date->format('Y-m-d') }}"
                       class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900" />
            </div>

            <div>
                <label class="text-xs text-gray-700">Amount</label>
                <input name="amount"
                       value="{{ $payment->amount }}"
                       type="number"
                       step="0.01"
                       min="0.01"
                       class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900" />
            </div>

            <div>
                <label class="text-xs text-gray-700">Cash Account</label>
                <select name="cash_account_id"
                        class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                    @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ $a->id == $payment->cash_account_id ? 'selected' : '' }}>
                            {{ $a->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">AP Account</label>
                <select name="ap_account_id"
                        class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900">
                    @foreach($accounts as $a)
                        <option value="{{ $a->id }}" {{ $a->id == $payment->ap_account_id ? 'selected' : '' }}>
                            {{ $a->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-700">Reference</label>
                <input name="reference"
                       value="{{ $payment->reference }}"
                       class="mt-1 p-2 w-full border border-gray-300 rounded bg-white text-gray-900" />
            </div>

        </div>

        <div class="mt-4">
            <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">Save</button>
            <a href="{{ route('supplierpayments.index') }}"
               class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
               Cancel
            </a>
        </div>

    </form>
</div>
@endsection
