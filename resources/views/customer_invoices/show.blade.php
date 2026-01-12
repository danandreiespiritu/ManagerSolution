<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Invoice {{ $invoice->invoice_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Invoice {{ $invoice->invoice_number }}</h1>
                <div class="space-x-2">
                    <a href="{{ route('customerinvoices.edit', $invoice->id) }}" class="px-3 py-1 bg-yellow-500 rounded">Edit</a>
                    <a href="{{ route('customerinvoices.index') }}" class="px-3 py-1 bg-gray-700 rounded">Back</a>
                </div>
            </div>

            <div class="bg-[#111827] p-6 rounded">
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-400">Customer</dt>
                        <dd class="text-lg text-white">{{ $invoice->customer?->customer_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Invoice Date</dt>
                        <dd class="text-white">{{ $invoice->invoice_date?->format('Y-m-d') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Due Date</dt>
                        <dd class="text-white">{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-400">Total</dt>
                        <dd class="text-white">{{ number_format($invoice->total_amount,2) }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <h3 class="font-semibold mb-2 text-white">Notes</h3>
                    <div class="bg-gray-800 p-3 rounded text-white">{{ $invoice->status ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
