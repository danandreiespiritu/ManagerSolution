<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10">
        <main class="max-w-5xl mx-auto px-4">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    General Ledger Transactions
                    <span class="text-gray-400" title="Create a General Ledger report for a selected date range and account.">
                        <i class="far fa-question-circle"></i>
                    </span>
                </h1>
                <p class="text-gray-600 mt-1 text-sm">Generate a ledger report based on date range and account selection.</p>
            </div>

            <!-- Card Container -->
            <div class="bg-white shadow-md rounded-lg border border-gray-200">
                <form action="{{ route('reports.general-ledger.transactions.store') }}"
                      method="POST" 
                      class="p-8 space-y-6">
                    @csrf

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <input id="description" 
                               name="description" 
                               type="text"
                               value="{{ old('description') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                               placeholder="Optional short description" />
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="from_date" class="block text-sm font-medium text-gray-700">From</label>
                            <input id="from_date"
                                   name="from_date"
                                   type="date"
                                   value="{{ old('from_date', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('from_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="to_date" class="block text-sm font-medium text-gray-700">To</label>
                            <input id="to_date"
                                   name="to_date"
                                   type="date"
                                   value="{{ old('to_date', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('to_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Account Selector -->
                    <div class="max-w-md">
                        <label for="account_id" class="block text-sm font-medium text-gray-700">Account</label>
                        <select id="account_id" 
                                name="account_id"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Select Account --</option>
                            @isset($accounts)
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ ($account->code ? $account->code . ' - ' : '') . ($account->name ?? 'Account') }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex gap-4">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 shadow">
                            Create Report
                        </button>

                        <a href="{{ route('reports.general-ledger.transactions.index') }}"
                           class="px-5 py-2.5 rounded-md border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-100">
                            Back to Reports
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>
