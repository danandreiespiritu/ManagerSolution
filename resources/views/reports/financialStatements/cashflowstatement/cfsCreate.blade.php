<x-app-layout>
<body class="bg-gray-50">
    
    <div class="flex min-h-screen bg-gray-50">

        <div class="flex-1 flex flex-col">
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                
                <!-- Page Header -->
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 id="pageHeading" class="text-2xl lg:text-3xl font-bold text-gray-900">
                                <i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>
                                Create Cash Flow Statement
                            </h1>
                            <p class="text-sm md:text-base text-gray-600 mt-2">
                                Generate a comprehensive cash flow statement for your business
                            </p>
                        </div>

                        <div>
                            <a href="{{ route('reports.financial.cashflow.index') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-arrow-left"></i>
                                Back to Reports
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-md">
                    <form action="{{ route('reports.financial.cashflow.store') }}" 
                          method="POST" 
                          id="cashFlowForm"
                          class="p-6 md:p-8 space-y-8">
                        @csrf

                        <!-- Basic Information -->
                        <div class="form-section pl-6 pr-4 py-4 rounded-lg">

                            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Basic Information
                            </h2>

                            <div class="space-y-5">

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Report Description <span class="text-gray-400">(Optional)</span>
                                    </label>
                                    <input id="description" type="text" name="description"
                                           value="{{ old('description') }}"
                                           class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>

                                <div class="max-w-md">
                                    <label for="method" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Calculation Method <span class="text-red-500">*</span>
                                    </label>
                                    <select id="method" name="method" required
                                            class="w-full border-gray-300 rounded-lg shadow-sm">
                                        <option value="indirect">Indirect Method</option>
                                        <option value="direct">Direct Method</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- Reporting Period -->
                        <div class="form-section pl-6 pr-4 py-4 rounded-lg">

                            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                                Reporting Period
                            </h2>

                            <div id="columnsContainer" class="space-y-4">

                                <!-- Main Period Row -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end max-w-xl">

                                        <div class="sm:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700">From</label>
                                            <input id="from" type="date" name="from"
                                                   value="{{ old('from') }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm">
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700">To</label>
                                            <input id="to" type="date" name="to"
                                                   value="{{ old('to') }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm">
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label class="block text-sm font-medium text-gray-700">Column name</label>
                                            <input id="column_label" type="text" name="column_label"
                                                   value="{{ old('column_label') }}"
                                                   class="w-full border-gray-300 rounded-md shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Footer and Options -->
                        <div class="form-section pl-6 pr-4 py-4 rounded-lg">

                            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-cog text-blue-600 mr-2"></i>
                                Display Options
                            </h2>

                            <div class="space-y-4">
                                <label for="footer" class="block text-sm font-medium text-gray-700">Footer</label>
                                <textarea id="footer" name="footer" rows="4"
                                          class="w-full border-gray-300 rounded-md shadow-sm">{{ old('footer') }}</textarea>
                            </div>

                        </div>

                        <!-- Submit -->
                        <div class="pt-6 border-t border-gray-200 flex justify-between">

                            <button id="submitBtn" type="submit"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                Create Report
                            </button>

                            <button type="reset" class="px-6 py-3 border rounded-lg text-gray-700">
                                Reset
                            </button>

                        </div>

                    </form>
                </div>
            </main>
        </div>
    </div>

</body>
</x-app-layout>
