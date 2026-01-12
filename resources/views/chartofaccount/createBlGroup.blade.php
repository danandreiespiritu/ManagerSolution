<x-app-layout>
    <div class="max-w-4xl mx-auto py-10">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <!-- HEADER -->
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">
                    Balance Sheet Group
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Create a new grouping for Balance Sheet accounts
                </p>
            </div>

            <!-- CONTENT -->
            <div class="px-6 py-6">

                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('BlGroupStore') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="is_active" value="1">
                    <!-- NAME -->
                    <div class="text-black">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Group Name
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Enter group name"
                            class="w-full rounded-lg border-gray-300 text-sm
                                   focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CATEGORY -->
                    <div class="text-black">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <input
                            list="bl_categories"
                            name="category"
                            value=""
                            placeholder="Select or type a category"
                            class="w-full rounded-lg border-gray-300 text-sm
                                   focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <datalist id="bl_categories">
                            <option value="Uncategorized"></option>

                            @php
                                $categories = collect($blgroup ?? [])
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
                    </div>

                    <!-- ACTIONS -->
                    <div class="flex justify-end pt-4 border-t">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2
                                   rounded-lg bg-indigo-600 px-6 py-2.5
                                   text-sm font-medium text-white
                                   hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Group
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
