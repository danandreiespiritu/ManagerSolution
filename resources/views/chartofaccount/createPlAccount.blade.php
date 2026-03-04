<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-5 bg-gray-50 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Create Profit &amp; Loss Account</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Add a new account under the Profit &amp; Loss category.
                </p>
            </div>

            <!-- Body -->
            <div class="px-6 py-8">

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-300 px-4 py-3 text-green-800 text-sm shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('PlAccountStore') }}" method="POST" class="space-y-6">
                    @csrf

                    <input type="hidden" name="account_type" value="PL">

                    <!-- Account Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Account Name <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="account_name"
                               value="{{ old('account_name') }}"
                               required
                               placeholder="e.g., Sales Revenue"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-900 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />

                        @error('account_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Account Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Account Code (optional)
                        </label>

                        <input type="text"
                               name="account_code"
                               value="{{ old('account_code') }}"
                               placeholder="e.g., 4010"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-900 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />

                        @error('account_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Account Group -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Account Group <span class="text-red-500">*</span>
                        </label>

                        <select name="account_group" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 text-sm
                                       focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">Select group</option>

                            @foreach(($groups ?? []) as $g)
                                <option value="{{ $g }}" {{ old('account_group') == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach
                        </select>

                        @error('account_group')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cash Flow Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cash Flow Category (optional)
                        </label>

                        <select name="cash_flow_category"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 text-sm
                                       focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">None / Select category</option>
                            <option value="Operating activities" {{ old('cash_flow_category') == 'Operating activities' ? 'selected' : '' }}>
                                Operating activities
                            </option>
                            <option value="Investing activities" {{ old('cash_flow_category') == 'Investing activities' ? 'selected' : '' }}>
                                Investing activities
                            </option>
                            <option value="Financing activities" {{ old('cash_flow_category') == 'Financing activities' ? 'selected' : '' }}>
                                Financing activities
                            </option>
                            <option value="Cash &amp; cash equivalents" {{ old('cash_flow_category') == 'Cash &amp; cash equivalents' ? 'selected' : '' }}>
                                Cash &amp; cash equivalents
                            </option>
                        </select>

                        @error('cash_flow_category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t">

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium shadow
                                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>

                            Create Account
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
