@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-xl font-semibold text-gray-800">Edit Supplier</h1>

    <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" class="mt-4 bg-white p-4 rounded border shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label class="text-xs text-gray-600">Name</label>
            <input name="supplier_name" value="{{ $supplier->supplier_name }}" class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm" />
        </div>

        <div class="mt-2">
            <label class="text-xs text-gray-600">Email</label>
            <input name="email" value="{{ $supplier->email }}" class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm" />
        </div>

        <div class="mt-2">
            <label class="text-xs text-gray-600">Code</label>
            <input name="supplier_code" value="{{ $supplier->supplier_code }}" class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm" />
        </div>

        <div class="mt-4 flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
                Save
            </button>

            <a href="{{ route('suppliers.index') }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
