@foreach($recentEntries as $entryRow)
<tr class="entry-row hover:bg-gray-50">
    <td class="px-3 py-2">{{ $entryRow->entry_date->format('Y-m-d') }}</td>
    <td class="px-3 py-2">{{ $entryRow->reference_type }} {{ $entryRow->reference_id }}</td>
    <td class="px-3 py-2">{{ Str::limit($entryRow->description, 70) }}</td>
    <td class="px-3 py-2 text-right">{{ number_format($entryRow->lines->sum('debit_amount'),2) }}</td>
    <td class="px-3 py-2 text-right">{{ number_format($entryRow->lines->sum('credit_amount'),2) }}</td>
    <td class="px-3 py-2 text-center">
        @php
            $isUnbalanced = $entryRow->hasImbalance();
            $imbalance = $entryRow->getImbalanceAmount();
        @endphp
        @if(! $isUnbalanced)
            <span class="text-sm px-3 py-1 bg-green-100 text-green-800 rounded">Balanced</span>
        @else
            <span class="text-sm px-3 py-1 bg-red-100 text-red-800 rounded">Unbalanced ({{ number_format(abs($imbalance),2) }})</span>
        @endif
    </td>
    <td class="px-3 py-2 text-center space-x-3">
        <a href="{{ route('journal.show', $entryRow->id) }}" class="text-blue-600">View</a>
        <a href="{{ route('journal.edit', $entryRow->id) }}" class="text-yellow-600">Edit</a>

        <form action="{{ route('journal.destroy', $entryRow->id) }}" method="POST" class="inline-block delete-entry-form">
            @csrf @method('DELETE')
            <button type="button" class="text-red-600 deleteBtn">Delete</button>
        </form>
    </td>
</tr>
@endforeach