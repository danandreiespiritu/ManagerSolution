<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

            <!-- HEADER -->
            <div class="px-6 py-5 bg-gray-50 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Create Profit &amp; Loss Group</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Add a new group structure for Profit &amp; Loss accounts.
                </p>
            </div>

            <!-- BODY -->
            <div class="px-6 py-8">

                <!-- SUCCESS MESSAGE -->
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-300 px-4 py-3 text-green-800 text-sm shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('PlGroupStore') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="is_active" value="1">

                    <!-- GROUP NAME -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Group Name <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Enter group name"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-900
                                   focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                        />

                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CATEGORY -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>

                        <input
                            list="pl_categories"
                            name="category"
                            placeholder="Select or type a category"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-900
                                   focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                        />

                        <datalist id="pl_categories">
                            <option value="Uncategorized"></option>

                            @php
                                $categories = collect($plgroup ?? [])
                                    ->pluck('group')
                                    ->filter(fn ($value) => filled($value))
                                    ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value))
                                    ->unique()
                                    ->values();
                            @endphp

                            @foreach($categories as $category)
                                <option value="{{ $category }}"></option>
                            @endforeach
                        </datalist>

                        @error('category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex justify-end pt-6 border-t">

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium shadow
                                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Group
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
