<x-app-layout>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Invoice</h1>
                    <p class="text-sm text-gray-600">Modify invoice details and update billing information.</p>
                </div>

                <a href="{{ route('customerinvoices.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow text-sm">
                    Back
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg shadow-sm">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

                <form action="{{ route('customerinvoices.update', $invoice->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Invoice Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number</label>
                        <input name="invoice_number"
                               required
                               value="{{ old('invoice_number', $invoice->invoice_number) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Invoice Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                        <input type="date"
                               name="invoice_date"
                               required
                               value="{{ old('invoice_date', optional($invoice->invoice_date)->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date"
                               name="due_date"
                               value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Total Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                        <input name="total_amount"
                               required
                               value="{{ old('total_amount', $invoice->total_amount) }}"
                               placeholder="0.00"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Status / Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status / Notes</label>
                        <input name="status"
                               value="{{ old('status', $invoice->status) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between items-center border-t pt-6">

                        <a href="{{ route('customerinvoices.index') }}"
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow text-sm">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow text-sm font-medium
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Save Changes
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
