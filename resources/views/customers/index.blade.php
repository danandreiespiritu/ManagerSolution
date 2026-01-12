<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage your customer database</p>
                </div>
                <div>
                    <button id="openAdd" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Add Customer</button>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded shadow border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Code</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Name</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Email</th>
                            <th class="px-4 py-3 text-left text-gray-900 font-semibold">Active</th>
                            <th class="px-4 py-3 text-right text-gray-900 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $customer->customer_code }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $customer->customer_name }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $customer->email }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $customer->is_active ? 'Yes' : 'No' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="inline-block px-3 py-1 bg-yellow-100 hover:bg-yellow-200 border border-yellow-300 text-yellow-800 rounded mr-2">Edit</a>
                                    <a href="{{ route('customerinvoices.bycustomer', $customer->id) }}" class="inline-block px-3 py-1 bg-indigo-100 hover:bg-indigo-200 border border-indigo-300 text-indigo-800 rounded mr-2">Invoices</a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete customer?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-100 hover:bg-red-200 border border-red-300 text-red-800 rounded">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-600">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Add modal -->
            <div id="addModal" class="fixed inset-0 hidden items-center justify-center z-50">
                <div class="absolute inset-0 bg-black/50" id="backdrop"></div>
                <div class="relative bg-white text-gray-900 rounded-lg w-full max-w-md p-6 z-10">
                    <h3 class="text-lg font-semibold mb-4">Create Customer</h3>
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm">Customer Code</label>
                                <input name="customer_code" value="{{ old('customer_code') }}" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm">Customer Name</label>
                                <input name="customer_name" required value="{{ old('customer_name') }}" class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm">Email</label>
                                <input name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" id="closeAdd" class="px-4 py-2 border rounded">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const open = document.getElementById('openAdd');
            const close = document.getElementById('closeAdd');
            const modal = document.getElementById('addModal');
            const backdrop = document.getElementById('backdrop');
            function show(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
            function hide(){ modal.classList.remove('flex'); modal.classList.add('hidden'); }
            open && open.addEventListener('click', show);
            close && close.addEventListener('click', hide);
            backdrop && backdrop.addEventListener('click', hide);
        })();
    </script>
</x-app-layout>
