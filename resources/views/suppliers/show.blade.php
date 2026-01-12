@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto text-gray-900">
    <h1 class="text-xl font-semibold text-gray-800">Supplier: {{ $supplier->supplier_name }}</h1>

    <div class="mt-4 bg-white p-4 rounded border shadow-sm">
        <p><strong>Code:</strong> {{ $supplier->supplier_code }}</p>
        <p><strong>Email:</strong> {{ $supplier->email }}</p>
        <p><strong>Active:</strong> {{ $supplier->is_active ? 'Yes' : 'No' }}</p>
    </div>

    <div class="mt-4 flex gap-2">
        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="px-3 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Edit
        </a>

        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="px-3 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700"
                    onclick="return confirm('Delete this supplier?')">Delete</button>
        </form>
    </div>
</div>
@endsection
