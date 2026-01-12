<x-app-layout>
    <div class="flex min-h-screen bg-gray-50">

            <div class="flex-1 flex flex-col">
                <div class="px-6 py-6">
                    <div class="flex items-center gap-2 mb-5">
                        <h1 class="text-xl font-semibold text-gray-900">Reports</h1>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <!-- Financial Statements -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-2 text-xs font-semibold tracking-wide uppercase text-gray-600 bg-gray-100">Financial Statements</div>
                            <ul class="divide-y divide-gray-100">
                                <li>
                                    <a href="{{ Route::has('reports.financialStatements.profit-and-loss.index') ? route('reports.financialStatements.profit-and-loss.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Profit and Loss Statement</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.financialStatements.profit-and-loss.actual-and-budget.index') ? route('reports.financialStatements.profit-and-loss.actual-and-budget.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Profit and Loss Statement (Actual vs Budget)</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.financial.balance-sheet.index') ? route('reports.financial.balance-sheet.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Balance Sheet</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.financial.cashflow.index') ? route('reports.financial.cashflow.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Cash Flow Statement</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.financial.changes-in-equity.index') ? route('reports.financial.changes-in-equity.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Statement of Changes in Equity</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Suppliers -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-2 text-xs font-semibold tracking-wide uppercase text-gray-600 bg-gray-100">Suppliers</div>
                            <ul class="divide-y divide-gray-100">
                                <li>
                                    <a href="{{ Route::has('reports.supplier-summary.index') ? route('reports.supplier-summary.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Supplier Summary</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.suppliers.statement-unpaid') ? route('reports.suppliers.statement-unpaid') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Supplier Statements (Unpaid Invoices)</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.suppliers.statement-transactions') ? route('reports.suppliers.statement-transactions') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Supplier Statements (Transactions)</a>
                                </li>
                            </ul>
                        </div>
                        <!-- General Ledger -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-2 text-xs font-semibold tracking-wide uppercase text-gray-600 bg-gray-100">General Ledger</div>
                            <ul class="divide-y divide-gray-100">
                                <li>
                                    <a href="{{ Route::has('reports.general-ledger.trial-balance.index') ? route('reports.general-ledger.trial-balance.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Trial Balance</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.general-ledger.summary.index') ? route('reports.general-ledger.summary.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">General Ledger Summary</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.general-ledger.transactions.index') ? route('reports.general-ledger.transactions.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">General Ledger Transactions</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Customers -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-2 text-xs font-semibold tracking-wide uppercase text-gray-600 bg-gray-100">Customers</div>
                            <ul class="divide-y divide-gray-100">
                                <li>
                                    <a href="{{ Route::has('reports.customer-summary.index') ? route('reports.customer-summary.index') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Customer Summary</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.customers.statement-unpaid') ? route('reports.customers.statement-unpaid') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Customer Statements (Unpaid Invoices)</a>
                                </li>
                                <li>
                                    <a href="{{ Route::has('reports.customers.statement-transactions') ? route('reports.customers.statement-transactions') : '#' }}" class="block px-4 py-3 text-sm text-blue-700 hover:bg-gray-50">Customer Statements (Transactions)</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>