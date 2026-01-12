<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Edit Payment</h2>
    </x-slot>

    <div class="py-6 text-white">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 p-6 rounded">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-600 text-white rounded">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-600 text-white rounded">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('payments.update', $payment->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Date</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Customer</label>
                            <select name="customer_id" class="w-full border rounded px-3 py-2">
                                <option value="">-- Select customer --</option>
                                @foreach($customers as $c)
                                    <option class="text-black" value="{{ $c->id }}" @selected(old('customer_id', $payment->customer_id) == $c->id)>{{ $c->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-4">
                            <label class="block text-sm text-white">Amount</label>
                            <input type="text" name="amount" value="{{ old('amount', $payment->amount) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>

                        <div class="col-span-4">
                            <label class="block text-sm text-white">Payment Type</label>
                            <select name="payment_type" class="w-full border rounded px-3 py-2 text-white">
                                <option class="text-black" value="Customer" @selected(old('payment_type', $payment->payment_type) == 'Customer')>Customer</option>
                                <option class="text-black" value="Supplier" @selected(old('payment_type', $payment->payment_type) == 'Supplier')>Supplier</option>
                            </select>
                        </div>

                        <div class="col-span-8">
                            <label class="block text-sm text-white">Reference</label>
                            <input type="text" name="reference" value="{{ old('reference', $payment->reference) }}" class="w-full border rounded px-3 py-2 text-white">
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('payments.show', $payment->id) }}" class="px-4 py-2 border rounded">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded">Save Changes</button>
                    </div>

                    @if($payment->invoicePayments->count())
                        <div class="mt-6 text-sm text-gray-300">
                            Allocations exist for this payment. Editing allocations is not supported here.
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
