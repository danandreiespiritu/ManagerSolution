<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Customers</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage your customer database</p>
                </div>

                <button id="openAdd"
                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow">
                    + Add Customer
                </button>
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

            <!-- Table Container -->
            <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">

                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-gray-800">Code</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-800">Name</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-800">Email</th>
                            <th class="px-5 py-3 text-left font-semibold text-gray-800">Active</th>
                            <th class="px-5 py-3 text-right font-semibold text-gray-800">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-gray-900">{{ $customer->customer_code }}</td>
                                <td class="px-5 py-3 text-gray-900">{{ $customer->customer_name }}</td>
                                <td class="px-5 py-3 text-gray-900">{{ $customer->email }}</td>
                                <td class="px-5 py-3">
                                    @if($customer->is_active)
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded border border-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded border border-red-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right space-x-2">

                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                        class="inline-block px-3 py-1 text-sm bg-yellow-100 hover:bg-yellow-200 text-yellow-800 border border-yellow-300 rounded-lg shadow-sm">
                                        Edit
                                    </a>

                                    <a href="{{ route('customerinvoices.bycustomer', $customer->id) }}"
                                        class="inline-block px-3 py-1 text-sm bg-indigo-100 hover:bg-indigo-200 text-indigo-800 border border-indigo-300 rounded-lg shadow-sm">
                                        Invoices
                                    </a>

                                    <form action="{{ route('customers.destroy', $customer->id) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Delete this customer?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 text-sm bg-red-100 hover:bg-red-200 text-red-800 border border-red-300 rounded-lg shadow-sm">
                                            Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-gray-500 text-sm">
                                    No customers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            <!-- Add Customer Modal -->
            <div id="addModal" class="fixed inset-0 hidden items-center justify-center z-50">
                <div id="backdrop" class="absolute inset-0 bg-black/50"></div>

                <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md p-6 z-10">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Create Customer</h3>

                    <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Code</label>
                            <input type="text" name="customer_code"
                                value="{{ old('customer_code') }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name"
                                value="{{ old('customer_name') }}" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email"
                                value="{{ old('email') }}"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button id="closeAdd"
                                type="button"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg shadow-sm text-sm">
                                Cancel
                            </button>

                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow text-sm">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        (function(){
            const modal = document.getElementById('addModal');
            const backdrop = document.getElementById('backdrop');
            const openBtn = document.getElementById('openAdd');
            const closeBtn = document.getElementById('closeAdd');

            function showModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function hideModal() {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }

            openBtn.addEventListener('click', showModal);
            closeBtn.addEventListener('click', hideModal);
            backdrop.addEventListener('click', hideModal);
        })();
    </script>
</x-app-layout>
