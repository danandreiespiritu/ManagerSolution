<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">Edit Customer</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-white">Edit Customer</h1>
                <a href="{{ route('customers.index') }}" class="px-3 py-1 bg-gray-700 rounded">Back</a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-600 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-[#111827] p-6 rounded">
                <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-300">Customer Code</label>
                            <input name="customer_code" value="{{ old('customer_code', $customer->customer_code) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300">Customer Name</label>
                            <input name="customer_name" required value="{{ old('customer_name', $customer->customer_name) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-300">Email</label>
                            <input name="email" value="{{ old('email', $customer->email) }}" class="w-full border rounded px-3 py-2 text-gray-900">
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 text-gray-300"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}> Active</label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <a href="{{ route('customers.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
