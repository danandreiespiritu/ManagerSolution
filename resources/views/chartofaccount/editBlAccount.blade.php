<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">

            <!-- HEADER -->
            <div class="px-6 py-5 bg-gray-50 border-b">
                <h2 class="text-2xl font-bold text-gray-800">Edit Balance Sheet Account</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Update details for this Balance Sheet account.
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

                <form action="{{ route('BlAccountUpdate', $account->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="account_type" value="BL">

                    <!-- ACCOUNT NAME -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Account Name <span class="text-red-500">*</span>
                        </label>

                        <input type="text"
                               name="account_name"
                               value="{{ old('account_name', $account->account_name) }}"
                               required
                               placeholder="e.g., Cash at Bank"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-900 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />

                        @error('account_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ACCOUNT CODE -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Account Code (optional)
                        </label>

                        <input type="text"
                               name="account_code"
                               value="{{ old('account_code', $account->account_code) }}"
                               placeholder="e.g., 1010"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-gray-900 text-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />

                        @error('account_code')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ACCOUNT GROUP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Account Group <span class="text-red-500">*</span>
                        </label>

                        <select name="account_group" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 text-sm
                                       focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">

                            <option value="">Select group</option>

                            @foreach(($groups ?? []) as $g)
                                <option value="{{ $g }}"
                                    {{ old('account_group', $account->account_group) == $g ? 'selected' : '' }}>
                                    {{ $g }}
                                </option>
                            @endforeach

                        </select>

                        @error('account_group')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CASH FLOW CATEGORY -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cash Flow Category (optional)
                        </label>

                        <select name="cash_flow_category"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-900 text-sm
                                       focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">None / Select category</option>

                            <option value="Operating activities"
                                {{ old('cash_flow_category', $account->cash_flow_category) == 'Operating activities' ? 'selected' : '' }}>
                                Operating activities
                            </option>

                            <option value="Investing activities"
                                {{ old('cash_flow_category', $account->cash_flow_category) == 'Investing activities' ? 'selected' : '' }}>
                                Investing activities
                            </option>

                            <option value="Financing activities"
                                {{ old('cash_flow_category', $account->cash_flow_category) == 'Financing activities' ? 'selected' : '' }}>
                                Financing activities
                            </option>

                            <option value="Cash &amp; cash equivalents"
                                {{ old('cash_flow_category', $account->cash_flow_category) == 'Cash &amp; cash equivalents' ? 'selected' : '' }}>
                                Cash &amp; cash equivalents
                            </option>
                        </select>

                        @error('cash_flow_category')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- BUTTONS -->
                    <div class="flex justify-between items-center pt-6 border-t">

                        <a href="{{ route('chartofaccountIndex') }}"
                           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700
                                  hover:bg-gray-100 focus:ring-2 focus:ring-indigo-400">
                            Cancel
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium shadow
                                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                            </svg>

                            Update Account
                        </button>

                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
