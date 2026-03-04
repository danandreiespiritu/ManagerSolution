<x-app-layout>
    <div class="max-w-5xl mx-auto p-6">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Statement</h1>
                    @if($customer)
                        <div class="mt-2">
                            <div class="font-semibold">{{ $customer->name }}</div>
                            <div class="text-gray-600">{{ $customer->address ?? '' }}</div>
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
                            <div class="whitespace-pre-line">{{ $customer->address ?? '' }}</div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('reports.customers.statement-unpaid.export', ['report' => $report->id] + (request()->has('customer_id') ? ['customer_id'=>request('customer_id')] : [])) }}"
                           class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700">
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full w-full text-sm border-collapse">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 text-left" style="width:120px">Date</th>
                        <th class="p-2 text-left" style="width:140px">Invoice</th>
                        <th class="p-2 text-left">Description</th>
                        <th class="p-2 text-left" style="width:140px">Invoice total</th>
                        <th class="p-2 text-left" style="width:110px">Overdue</th>
                        <th class="p-2 text-left" style="width:120px">Status</th>
                        <th class="p-2 text-left" style="width:160px">Balance due</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="p-2">{{ optional($inv->issue_date)->format('d/m/Y') }}</td>
                            <td class="p-2">
                                <a href="{{ route('customerinvoices.show', $inv->id) }}" class="text-indigo-600 hover:underline">
                                    {{ $inv->invoice_number ?? ('INV-'.$inv->id) }}
                                </a>
                            </td>
                            <td class="p-2">{{ $inv->description ?? '' }}</td>
                            <td class="p-2">{{ number_format((float)($inv->grand_total ?? 0), 2) }}</td>
                            <td class="p-2">{{ $inv->overdue_days > 0 ? $inv->overdue_days.' days' : '—' }}</td>
                            @php $st = strtolower((string)($inv->payment_status ?? '')); $badge = $st === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; @endphp
                            <td class="p-2"><span class="inline-block text-xs px-2 py-1 rounded {{ $badge }}">{{ $st ? ucfirst($st) : '—' }}</span></td>
                            <td class="p-2">{{ number_format((float)($inv->balance_due ?? ($inv->grand_total ?? 0)), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-500">No unpaid invoices up to {{ $statementDate->format('d/m/Y') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-8">
                <div></div>
                <div>
                    <table class="min-w-full text-sm">
                        <tbody>
                        <tr>
                            <td>Current</td>
                            <td class="text-right">{{ number_format($aging['current'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>1-30 days overdue</td>
                            <td class="text-right">{{ number_format($aging['bucket_1_30'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>31-60 days overdue</td>
                            <td class="text-right">{{ number_format($aging['bucket_31_60'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>61-90 days overdue</td>
                            <td class="text-right">{{ number_format($aging['bucket_61_90'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>90+ days overdue</td>
                            <td class="text-right">{{ number_format($aging['bucket_90_plus'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-semibold">Total</td>
                            <td class="text-right font-semibold">{{ number_format($aging['total'], 2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>