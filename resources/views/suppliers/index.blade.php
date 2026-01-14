@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Suppliers</h1>
            <p class="text-sm text-gray-500">Manage your supplier master data and quick actions.</p>
        </div>
       
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 border border-green-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex gap-3 text-gray-900">
        <form method="GET" class="flex-1">
            <input
                name="q"
                value="{{ request('q') }}"
                placeholder="Search suppliers..."
                class="w-full p-2 bg-white border border-gray-300 rounded shadow-sm"
            />
        </form>
        <a href="{{ route('suppliers.index') }}"
           class="px-3 py-2 bg-blue-100 text-blue-600 rounded text-sm border border-blue-300 hover:bg-blue-200">
            Refresh
        </a>
    </div>

    <div class="bg-white p-4 rounded-lg border shadow-sm">
        <div class="overflow-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b text-gray-700">
                        <th class="py-3 px-4 text-left font-semibold">Name</th>
                        <th class="py-3 px-4 text-left font-semibold">Email</th>
                        <th class="py-3 px-4 text-left font-semibold">Code</th>
                        <th class="py-3 px-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($suppliers ?? [] as $supplier)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $supplier->supplier_name }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $supplier->id }}</div>
                            </td>

                            <td class="py-3 px-4 text-gray-700">
                                {{ $supplier->email }}
                            </td>

                            <td class="py-3 px-4 text-gray-700">
                                {{ $supplier->supplier_code }}
                            </td>

                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center gap-2">

                                    <!-- View -->
                                    <a href="{{ route('suppliers.show', $supplier->id) }}"
                                    class="px-3 py-1.5 bg-blue-100 text-blue-600 text-xs rounded-md hover:bg-blue-200 transition">
                                        View
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                    class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-xs rounded-md hover:bg-yellow-200 transition">
                                        Edit
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            class="px-3 py-1.5 bg-red-100 text-red-700 text-xs rounded-md hover:bg-red-200 transition"
                                            onclick="openDeleteModal('{{ route('suppliers.destroy', $supplier->id) }}')"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-500">
                                No suppliers yet. Use “New Supplier” to add one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal"
        class="fixed inset-0 bg-white/10 bg-opacity-40 backdrop-blur hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-sm rounded-lg shadow-lg p-6 text-gray-800">
            <h2 class="text-lg font-semibold mb-2">Confirm Deletion</h2>
            <p class="text-sm text-gray-600">
                Are you sure you want to delete this item? This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                    Cancel
                </button>

                <form id="deleteForm" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $suppliers->appends(request()->query())->links() }}
    </div>

    <!-- Create Supplier Form -->
    <div id="new-supplier"
        class="mt-10 bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">

        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            Create Supplier
        </h3>

        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-600">Supplier Name</label>
                    <input
                        name="supplier_name"
                        value="{{ old('supplier_name') }}"
                        class="mt-1 w-full rounded-lg border-gray-900 shadow-sm
                            focus:border-blue-500 focus:ring-blue-500 bg-white px-3 py-2"
                        placeholder="Enter supplier name"
                    />
                    @error('supplier_name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <input
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm
                            focus:border-blue-500 focus:ring-blue-500 bg-white px-3 py-2"
                        placeholder="name@example.com"
                    />
                    @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-600">Code</label>
                    <input
                        name="supplier_code"
                        value="{{ old('supplier_code') }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm
                            focus:border-blue-500 focus:ring-blue-500 bg-white px-3 py-2"
                        placeholder="SUP-001"
                    />
                    @error('supplier_code')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow
                        hover:bg-blue-700 transition-all focus:ring-4 focus:ring-blue-200">
                    Create Supplier
                </button>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.querySelector("input[name='q']");
    const rows = document.querySelectorAll("tbody tr");

    searchInput.addEventListener("keyup", function () {
        const search = this.value.toLowerCase();

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(search) ? "" : "none";
        });
    });

});

function openDeleteModal(url) {
    document.getElementById('deleteModal').style.display = 'flex';
    document.getElementById('deleteForm').action = url;
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal if clicking outside the box
document.addEventListener('click', function(e) {
    const modal = document.getElementById('deleteModal');
    const box = modal.querySelector('div');

    if (e.target === modal) {
        closeDeleteModal();
    }
});
</script>

@endsection
