<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Title -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Payment</h1>
                <p class="text-gray-600 text-sm mt-1">
                    Update payment information in a clean and organized layout.
                </p>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white border border-gray-200 rounded-xl shadow p-8">

                <form method="POST" action="{{ route('payments.update', $payment->id) }}" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                            <input 
                                type="date"
                                name="payment_date"
                                value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>

                        <!-- Customer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <select 
                                name="customer_id"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option 
                                        value="{{ $c->id }}"
                                        @selected(old('customer_id', $payment->customer_id) == $c->id)
                                    >
                                        {{ $c->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                            <input 
                                type="text"
                                name="amount"
                                value="{{ old('amount', $payment->amount) }}"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>

                        <!-- Payment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                            <select 
                                name="payment_type"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="Customer" @selected(old('payment_type', $payment->payment_type) == 'Customer')>
                                    Customer
                                </option>
                                <option value="Supplier" @selected(old('payment_type', $payment->payment_type) == 'Supplier')>
                                    Supplier
                                </option>
                            </select>
                        </div>

                        <!-- Reference -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                            <input 
                                type="text"
                                name="reference"
                                value="{{ old('reference', $payment->reference) }}"
                                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>

                    </div>

                    <!-- Notice: Allocations cannot be edited here -->
                    @if($payment->invoicePayments->count())
                        <div class="mt-6 p-4 bg-gray-50 border border-gray-300 text-gray-700 rounded-lg text-sm">
                            This payment has existing invoice allocations. Editing allocations is not available on this screen.
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">

                        <a href="{{ route('payments.show', $payment->id) }}"
                           class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-800 hover:bg-gray-200 transition">
                            Cancel
                        </a>

                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                            Save Changes
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
