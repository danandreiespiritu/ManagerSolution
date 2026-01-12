@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <h1 class="text-xl font-semibold text-gray-900">
        Supplier Debit Note #{{ $note->debit_note_number }}
    </h1>

    <div class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm text-gray-900">
        <p><strong>Supplier:</strong> {{ $note->supplier->supplier_name ?? '—' }}</p>
        <p><strong>Date:</strong> {{ optional($note->debit_date)->format('Y-m-d') }}</p>
        <p><strong>Amount:</strong> {{ number_format($note->total_amount,2) }}</p>
        <p><strong>Reason:</strong> {{ $note->reason }}</p>
        <p><strong>Status:</strong> {{ $note->status }}</p>
    </div>

    <div class="mt-4 bg-white p-4 rounded border border-gray-200 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Allocations</h2>

        @if($note->bills && $note->bills->count())
            <table class="w-full text-sm mt-2">
                <thead class="text-left text-gray-700 text-xs uppercase border-b">
                    <tr>
                        <th>Bill</th>
                        <th>Date</th>
                        <th>Amount Applied</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($note->bills as $b)
                    @php $pivot = $b->pivot; @endphp

                    <tr class="border-b hover:bg-gray-50 text-gray-900">
                        <td class="py-2">
                            <a href="{{ route('supplierbills.show', $b->id) }}" class="text-blue-600 hover:underline">
                                {{ $b->bill_number ?? '#'.$b->id }}
                            </a>
                        </td>

                        <td class="py-2">{{ optional($b->bill_date)->format('Y-m-d') }}</td>
                        <td class="py-2">{{ number_format($pivot->amount,2) }}</td>
                    </tr>

                @endforeach
                </tbody>

            </table>

        @else
            <p class="text-gray-600 mt-1">No allocations.</p>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('supplierdebitnotes.edit', $note->id) }}"
           class="px-3 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Edit
        </a>

        <form action="{{ route('supplierdebitnotes.destroy', $note->id) }}"
              method="POST"
              class="inline-block ml-2">
            @csrf
            @method('DELETE')

            <button class="px-3 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700"
                    onclick="return confirm('Delete this debit note?')">
                Delete
            </button>
        </form>
    </div>

</div>
@endsection
