<x-app-layout>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar (if any) will be injected by layout -->

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">

                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-semibold text-gray-900">
                                Supplier Statement (Unpaid Invoices)
                            </h1>
                            <span class="inline-flex items-center justify-center h-6 w-6 text-gray-400 border border-gray-200 rounded-full text-xs"
                                  title="Generate supplier summary report">?</span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">
                            Generate a summary of supplier transactions for a specific period
                        </p>
                    </div>

                    <!-- Form -->
                    <div class="px-6 py-6">
                        <form method="POST" action="{{ route('reports.suppliers.statement-unpaid.generate') }}" class="space-y-6">
                            @csrf

                            <!-- Date Range -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="Date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                    <input type="date"
                                           id="Date"
                                           name="Date"
                                           value="{{ old('Date', date('Y-m-d')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           required>

                                    @error('Date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-4">

                                <a href="{{ route('reports.suppliers.statement-unpaid.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Reports
                                </a>

                                <button type="submit"
                                        class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    Generate
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>
