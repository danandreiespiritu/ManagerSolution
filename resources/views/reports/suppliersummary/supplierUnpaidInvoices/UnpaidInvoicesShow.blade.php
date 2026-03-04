<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Supplier Statement (Unpaid Invoices)</h1>
            <div class="text-sm text-gray-500">Up to {{ $statementDate->format('d/m/Y') }}</div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">Statement</h2>
                        @if($supplier)
                            <div class="mt-2">
                                <div class="font-semibold">{{ $supplier->name }}</div>
                                <div class="text-gray-600">{{ $supplier->address ?? '' }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="text-sm text-gray-700 flex items-start gap-4">
                        <div class="flex gap-10">
                            <div>
                                <div class="font-medium">Date</div>
                                <div>{{ $statementDate->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="font-medium">Address</div>
                                <div class="whitespace-pre-line">{{ $supplier->address ?? '' }}</div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('reports.suppliers.statement-unpaid.export', ['report' => $report->id] + (request()->has('supplier_id') ? ['supplier_id'=>request('supplier_id')] : [])) }}"
                               class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="p-2 text-left" style="width:120px">Date</th>
                            <th class="p-2 text-left" style="width:140px">Invoice</th>
                            <th class="p-2 text-left">Description</th>
                            <th class="p-2 text-right" style="width:140px">Invoice total</th>
                            <th class="p-2 text-right" style="width:110px">Overdue</th>
                            <th class="p-2 text-left" style="width:120px">Status</th>
                            <th class="p-2 text-right" style="width:160px">Balance due</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($bills as $inv)
                            <tr class="border-t">
                                <td class="p-2">{{ optional($inv->issue_date)->format('d/m/Y') }}</td>
                                <td class="p-2">
                                    <a href="{{ route('user.supplier.purchaseInvoice.show', $inv->id) }}" class="text-indigo-600 hover:underline">
                                        {{ $inv->reference ?? ('INV-'.$inv->id) }}
                                    </a>
                                </td>
                                    <td class="p-2">{{ $inv->description ?? '' }}</td>
                                <td class="p-2 text-right">{{ number_format((float)($inv->grand_total ?? 0), 2) }}</td>
                                    <td class="p-2 text-right">{{ $inv->overdue_days > 0 ? $inv->overdue_days.' days' : '—' }}</td>
                                @php $st = strtolower((string)($inv->status ?? '')); $badge = $st === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; @endphp
                                <td class="p-2"><span class="inline-block text-xs px-2 py-1 rounded {{ $badge }}">{{ $st ? ucfirst($st) : '—' }}</span></td>
                                    <td class="p-2 text-right">{{ number_format((float)($inv->balance_due ?? ($inv->grand_total ?? 0)), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-500 p-4">No unpaid invoices up to {{ $statementDate->format('d/m/Y') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-8">
                    <div></div>
                    <div>
                        <table class="w-full text-sm border-collapse">
                            <tbody>
                            <tr>
                                <td class="p-2">Current</td>
                                <td class="p-2 text-right">{{ number_format($aging['current'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2">1-30 days overdue</td>
                                <td class="p-2 text-right">{{ number_format($aging['bucket_1_30'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2">31-60 days overdue</td>
                                <td class="p-2 text-right">{{ number_format($aging['bucket_31_60'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2">61-90 days overdue</td>
                                <td class="p-2 text-right">{{ number_format($aging['bucket_61_90'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2">90+ days overdue</td>
                                <td class="p-2 text-right">{{ number_format($aging['bucket_90_plus'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-semibold">Total</td>
                                <td class="p-2 text-right font-semibold">{{ number_format($aging['total'], 2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
