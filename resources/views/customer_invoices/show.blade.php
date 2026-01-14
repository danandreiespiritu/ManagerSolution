<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    Invoice #{{ $invoice->invoice_number }}
                </h1>

                <div class="space-x-2">
                    <a href="{{ route('customerinvoices.edit', $invoice->id) }}"
                       class="px-4 py-2 bg-yellow-100 text-yellow-700 border border-yellow-300 rounded-lg shadow text-sm hover:bg-yellow-200">
                        Edit
                    </a>

                    <a href="{{ route('customerinvoices.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg shadow text-sm hover:bg-gray-300">
                        Back
                    </a>
                </div>
            </div>

            <!-- Invoice Card -->
            <div class="bg-white border border-gray-200 rounded-xl shadow p-6">

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Customer -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Customer</dt>
                        <dd class="mt-1 text-lg text-gray-900">
                            {{ $invoice->customer?->customer_name ?? '—' }}
                        </dd>
                    </div>

                    <!-- Invoice Date -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Invoice Date</dt>
                        <dd class="mt-1 text-lg text-gray-900">
                            {{ optional($invoice->invoice_date)->format('Y-m-d') }}
                        </dd>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-lg text-gray-900">
                            {{ optional($invoice->due_date)->format('Y-m-d') ?? '—' }}
                        </dd>
                    </div>

                    <!-- Total -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ number_format($invoice->total_amount, 2) }}
                        </dd>
                    </div>

                </dl>

                <!-- Status -->
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Status / Notes</h3>
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-gray-800 text-sm">
                        {{ $invoice->status ?: '—' }}
                    </div>
                </div>

                <!-- Secondary Info (Optional) -->
                @if($invoice->reference_number ?? false)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Reference Number</h3>
                        <div class="p-3 bg-gray-50 border rounded text-gray-800 text-sm">
                            {{ $invoice->reference_number }}
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
