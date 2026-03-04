<x-app-layout>
    <div class="max-w-4xl mx-auto py-10">

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50/70 backdrop-blur">
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-semibold text-gray-900">
                        General Ledger Summary
                    </h1>
                    <span class="inline-flex items-center justify-center 
                        h-5 w-5 text-gray-400 border border-gray-200 rounded-full 
                        text-xs cursor-help">
                        ?
                    </span>
                </div>
            </div>

            <!-- Form -->
            <div class="px-6 py-6">
                <form action="{{ route('reports.general-ledger.summary.store') }}"
                      method="POST"
                      class="space-y-6 max-w-3xl">
                    @csrf

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <input type="text"
                               id="description"
                               name="description"
                               value="{{ old('description') }}"
                               placeholder="Optional"
                               class="w-full px-3 py-2 border border-gray-300 
                                      rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 
                                      focus:border-blue-500 transition-all">
                    </div>

                    <!-- Period -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2">From</label>
                            <input type="date"
                                   id="from_date"
                                   name="from_date"
                                   value="{{ old('from_date', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg 
                                          shadow-sm focus:ring-2 focus:ring-blue-500 
                                          focus:border-blue-500 transition-all">
                        </div>

                        <div>
                            <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                            <input type="date"
                                   id="to_date"
                                   name="to_date"
                                   value="{{ old('to_date', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg 
                                          shadow-sm focus:ring-2 focus:ring-blue-500 
                                          focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-3">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox"
                                   name="show_codes"
                                   value="1"
                                   class="form-checkbox text-indigo-600 rounded"
                                   {{ old('show_codes') ? 'checked' : '' }}>

                            <span class="text-gray-700 text-sm">Show account codes</span>
                        </label>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox"
                                   name="exclude_zero"
                                   value="1"
                                   class="form-checkbox text-indigo-600 rounded"
                                   {{ old('exclude_zero') ? 'checked' : '' }}>

                            <span class="text-gray-700 text-sm">Exclude zero balances</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 
                                       hover:bg-blue-700 text-white rounded-lg text-sm 
                                       shadow-sm transition-all active:scale-[.98]">
                            Create
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</x-app-layout>
