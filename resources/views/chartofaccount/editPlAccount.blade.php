<x-app-layout>
    <div class="max-w-4xl mx-auto py-10">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Edit Profit & Loss Account</h2>
            </div>

            <div class="px-6 py-6">

                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('PlAccountUpdate', $account->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="account_type" value="PL">

                    <div class="text-black">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                        <input type="text" name="account_name" value="{{ old('account_name', $account->account_name) }}" required
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. Sales Revenue" />
                        @error('account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="text-black">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Code (optional)</label>
                        <input type="text" name="account_code" value="{{ old('account_code', $account->account_code) }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g. 4010" />
                        @error('account_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="text-black">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Group</label>
                        <select name="account_group" required
                                class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 bg-white focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select group</option>
                            @foreach(($groups ?? []) as $g)
                                <option value="{{ $g }}" {{ old('account_group', $account->account_group) == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                        @error('account_group')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="text-black">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cash Flow Category (optional)</label>
                        <select name="cash_flow_category"
                                class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 bg-white focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">None / Select category</option>
                            <option value="Operating activities" {{ old('cash_flow_category', $account->cash_flow_category) == 'Operating activities' ? 'selected' : '' }}>Operating activities</option>
                            <option value="Investing activities" {{ old('cash_flow_category', $account->cash_flow_category) == 'Investing activities' ? 'selected' : '' }}>Investing activities</option>
                            <option value="Financing activities" {{ old('cash_flow_category', $account->cash_flow_category) == 'Financing activities' ? 'selected' : '' }}>Financing activities</option>
                            <option value="Cash &amp; cash equivalents" {{ old('cash_flow_category', $account->cash_flow_category) == 'Cash &amp; cash equivalents' ? 'selected' : '' }}>Cash &amp; cash equivalents</option>
                        </select>
                        @error('cash_flow_category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-between pt-4 border-t">
                        <a href="{{ route('chartofaccountIndex') }}" class="inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm">Cancel</a>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Update Account</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
