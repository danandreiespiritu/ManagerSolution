<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">Journal Entry</h2>
    </x-slot>

    <div class="py-6 text-black">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Date: {{ $entry->entry_date->format('Y-m-d') }}</div>
                    <div class="text-sm text-gray-600">Reference: {{ $entry->reference_type }} {{ $entry->reference_id }}</div>
                    <div class="text-sm text-gray-600">Description: {{ $entry->description }}</div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold">Lines</h3>
                        @php
                            $isUnbalanced = $entry->hasImbalance();
                            $imbalance = $entry->getImbalanceAmount();
                        @endphp
                        @if(! $isUnbalanced)
                            <span class="text-sm px-3 py-1 bg-green-100 text-green-800 rounded">Balanced</span>
                        @else
                            <span class="text-sm px-3 py-1 bg-red-100 text-red-800 rounded">Unbalanced ({{ number_format(abs($imbalance),2) }})</span>
                        @endif
                    </div>
                    <table class="w-full text-sm mt-2">
                        <thead>
                            <tr class="text-left text-xs text-gray-500">
                                <th class="px-2">Account</th>
                                <th class="px-2">Account Code</th>
                                <th class="px-2 text-right">Debit</th>
                                <th class="px-2 text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entry->lines as $ln)
                                <tr class="border-t">
                                    <td class="px-2 py-2">{{ optional($ln->account)->account_name }}</td>
                                    <td class="px-2 py-2">{{ optional($ln->account)->account_code }}</td>
                                    <td class="px-2 py-2 text-right">{{ number_format($ln->debit_amount,2) }}</td>
                                    <td class="px-2 py-2 text-right">{{ number_format($ln->credit_amount,2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="2" class="px-2 py-2 text-right font-semibold">Totals</td>
                                <td class="px-2 py-2 text-right font-semibold">{{ number_format($entry->lines->sum('debit_amount'),2) }}</td>
                                <td class="px-2 py-2 text-right font-semibold">{{ number_format($entry->lines->sum('credit_amount'),2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('journal.edit', $entry->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
                    <a href="{{ route('journal.index') }}" class="px-4 py-2 border rounded">Back</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
