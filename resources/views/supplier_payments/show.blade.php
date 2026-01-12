@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-xl font-semibold text-gray-900">Supplier Payment #{{ $payment->id }}</h1>

    <div class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm text-gray-900">
        <p><strong>Supplier:</strong> {{ $payment->supplier->supplier_name ?? '—' }}</p>
        <p><strong>Date:</strong> {{ optional($payment->payment_date)->format('Y-m-d') }}</p>
        <p><strong>Amount:</strong> {{ number_format($payment->amount, 2) }}</p>
        <p><strong>Allocated:</strong> {{ number_format($payment->allocatedAmount(), 2) }}</p>
        <p><strong>Unallocated:</strong> {{ number_format($payment->unallocatedAmount(), 2) }}</p>
        <p><strong>Method:</strong> {{ $payment->payment_method }}</p>
        <p><strong>Reference:</strong> {{ $payment->reference }}</p>
        <p><strong>Status:</strong> {{ $payment->status }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('supplierpayments.edit', $payment->id) }}"
           class="px-3 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Edit
        </a>

        <form action="{{ route('supplierpayments.destroy', $payment->id) }}"
              method="POST" class="inline-block ml-2">
            @csrf
            @method('DELETE')

            <button class="px-3 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700"
                    onclick="return confirm('Delete this payment?')">
                Delete
            </button>
        </form>
    </div>

</div>
@endsection
