<x-app-layout>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Customer</h1>

                <a href="{{ route('customers.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow text-sm">
                    Back
                </a>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-300 text-green-800 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-300 text-red-800 shadow-sm">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow border border-gray-200 p-6">

                <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Customer Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Code</label>
                        <input type="text"
                               name="customer_code"
                               value="{{ old('customer_code', $customer->customer_code) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Customer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="customer_name"
                               value="{{ old('customer_name', $customer->customer_name) }}"
                               required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $customer->email) }}"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-800 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>

                    <!-- Active Checkbox -->
                    <div class="flex items-center">
                        <label class="inline-flex items-center gap-2 text-gray-700">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   class="rounded"
                                   {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 border-t pt-6">

                        <a href="{{ route('customers.index') }}"
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
