<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Edit Invoice</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Edit Invoice</h1>
                <a href="{{ route('customerinvoices.index') }}" class="px-3 py-1 bg-gray-700 rounded">Back</a>
            </div>

            <div class="bg-[#111827] p-6 rounded">
                <form action="{{ route('customerinvoices.update', $invoice->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-300">Invoice Number</label>
                            <input name="invoice_number" required value="{{ old('invoice_number', $invoice->invoice_number) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300">Invoice Date</label>
                            <input type="date" name="invoice_date" required value="{{ old('invoice_date', optional($invoice->invoice_date)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300">Total Amount</label>
                            <input name="total_amount" required value="{{ old('total_amount', $invoice->total_amount) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300">Status / Notes</label>
                            <input name="status" value="{{ old('status', $invoice->status) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('customerinvoices.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
