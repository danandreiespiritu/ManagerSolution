@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-xl font-semibold text-gray-900">Supplier Bill #{{ $bill->bill_number }}</h1>

    <div class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm text-gray-900">
        <p><strong>Supplier:</strong> {{ $bill->supplier->supplier_name ?? '—' }}</p>
        <p><strong>Date:</strong> {{ optional($bill->bill_date)->format('Y-m-d') }}</p>
        <p><strong>Due:</strong> {{ optional($bill->due_date)->format('Y-m-d') }}</p>
        <p><strong>Amount:</strong> {{ number_format($bill->total_amount,2) }}</p>
        <p><strong>Applied:</strong> {{ number_format($bill->appliedAmount(),2) }}</p>
        <p><strong>Balance Due:</strong> {{ number_format($bill->balanceDue(),2) }}</p>
        <p><strong>Status:</strong> {{ $bill->status }}</p>
    </div>

    <!-- Allocations -->
    <div class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm text-gray-900">

        <h2 class="text-lg font-semibold mb-2">Allocations</h2>

        <!-- PAYMENTS -->
        <div class="mb-4">
            <h3 class="text-sm font-medium text-gray-900">Payments Applied</h3>

            @if($bill->billPayments->count())
                <table class="w-full text-sm mt-2">
                    <thead class="text-left text-gray-700 text-xs uppercase border-b">
                        <tr>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill->billPayments as $bp)
                            <tr class="border-b">
                                <td class="py-2">
                                    @if($bp->payment)
                                        <a href="{{ route('supplierpayments.show', $bp->payment->id) }}"
                                           class="text-blue-600 hover:underline">
                                            #{{ $bp->payment->id }}
                                        </a>
                                    @else
                                        #{{ $bp->supplier_payment_id }}
                                    @endif
                                </td>
                                <td class="py-2">{{ optional($bp->created_at)->format('Y-m-d') }}</td>
                                <td class="py-2">{{ number_format($bp->amount,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">No payments applied.</p>
            @endif
        </div>

        <!-- CREDIT NOTES -->
        <div class="mb-4">
            <h3 class="text-sm font-medium text-gray-900">Credit Notes Applied</h3>
            @if($bill->creditNoteAllocations->count())
                <table class="w-full text-sm mt-2">
                    <thead class="text-left text-gray-700 text-xs uppercase border-b">
                        <tr><th>Credit Note</th><th>Date</th><th>Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bill->creditNoteAllocations as $ca)
                            <tr class="border-b">
                                <td class="py-2">
                                    @if($ca->note)
                                        <a href="{{ route('suppliercreditnotes.show', $ca->note->id) }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $ca->note->credit_note_number ?? '#'.$ca->note->id }}
                                        </a>
                                    @else
                                        #{{ $ca->supplier_credit_note_id }}
                                    @endif
                                </td>
                                <td class="py-2">{{ optional($ca->created_at)->format('Y-m-d') }}</td>
                                <td class="py-2">{{ number_format($ca->amount,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">No credit notes applied.</p>
            @endif
        </div>

        <!-- DEBIT NOTES -->
        <div>
            <h3 class="text-sm font-medium text-gray-900">Debit Notes Applied</h3>
            @if($bill->debitNoteAllocations->count())
                <table class="w-full text-sm mt-2">
                    <thead class="text-left text-gray-700 text-xs uppercase border-b">
                        <tr><th>Debit Note</th><th>Date</th><th>Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bill->debitNoteAllocations as $da)
                            <tr class="border-b">
                                <td class="py-2">
                                    @if($da->note)
                                        <a href="{{ route('supplierdebitnotes.show', $da->note->id) }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $da->note->debit_note_number ?? '#'.$da->note->id }}
                                        </a>
                                    @else
                                        #{{ $da->supplier_debit_note_id }}
                                    @endif
                                </td>
                                <td class="py-2">{{ optional($da->created_at)->format('Y-m-d') }}</td>
                                <td class="py-2">{{ number_format($da->amount,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">No debit notes applied.</p>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('supplierbills.edit', $bill->id) }}"
           class="px-3 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Edit
        </a>

        <form action="{{ route('supplierbills.destroy', $bill->id) }}"
              method="POST" class="inline-block ml-2">
            @csrf
            @method('DELETE')
            <button class="px-3 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700"
                    onclick="return confirm('Delete this bill?')">
                Delete
            </button>
        </form>
    </div>

</div>
@endsection
