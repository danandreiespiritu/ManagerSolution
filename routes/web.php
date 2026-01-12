<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\AccountingPeriodController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfitAndLossReportController;
use App\Http\Controllers\BalanceSheetReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Accept accidental POSTs to `/` (e.g. forms with empty action) and redirect to login
Route::post('/', function () {
    return redirect()->route('login');
});
Route::get('/dashboard', [BusinessController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chart of account route
    Route::get('/chartofaccounts', [HomeController::class, 'chartofaccountIndex'])->name('chartofaccountIndex');
    Route::get('/chartofaccounts/create', [HomeController::class, 'chartofaccountCreateBlGroup'])->name('BlGroupCreate');
    Route::post('/chartofaccounts/store', [HomeController::class, 'chartofaccountStoreBlGroup'])->name('BlGroupStore');
    // BL Account create/store
    Route::get('/chartofaccounts/create-account', [HomeController::class, 'chartofaccountCreateBlAccount'])->name('BlAccountCreate');
    Route::post('/chartofaccounts/store-account', [HomeController::class, 'chartofaccountStoreBlAccount'])->name('BlAccountStore');
    Route::get('/chartofaccounts/{id}/edit-account', [HomeController::class, 'chartofaccountEditBlAccount'])->name('BlAccountEdit');
    Route::put('/chartofaccounts/{id}/update-account', [HomeController::class, 'chartofaccountUpdateBlAccount'])->name('BlAccountUpdate');
    Route::delete('/chartofaccounts/{id}/delete-account', [HomeController::class, 'chartofaccountDestroyBlAccount'])->name('BlAccountDestroy');
    // Profit & Loss (PL) group and account routes
    Route::get('/chartofaccounts/create-pl', [HomeController::class, 'chartofaccountCreatePlGroup'])->name('PlGroupCreate');
    Route::post('/chartofaccounts/store-pl', [HomeController::class, 'chartofaccountStorePlGroup'])->name('PlGroupStore');
    Route::get('/chartofaccounts/create-pl-account', [HomeController::class, 'chartofaccountCreatePlAccount'])->name('PlAccountCreate');
    Route::post('/chartofaccounts/store-pl-account', [HomeController::class, 'chartofaccountStorePlAccount'])->name('PlAccountStore');
    Route::get('/chartofaccounts/pl/{id}/edit-account', [HomeController::class, 'chartofaccountEditPlAccount'])->name('PlAccountEdit');
    Route::put('/chartofaccounts/pl/{id}/update-account', [HomeController::class, 'chartofaccountUpdatePlAccount'])->name('PlAccountUpdate');
    Route::delete('/chartofaccounts/pl/{id}/delete-account', [HomeController::class, 'chartofaccountDestroyPlAccount'])->name('PlAccountDestroy');
    
    Route::get('/businesses', [BusinessController::class, 'index'])->name('business.index');
    Route::get('/businesses/{id}/summary', [BusinessController::class, 'summary'])->name('business.summary');
    Route::post('/businesses/store', [BusinessController::class, 'store'])->name('business.store');
    Route::post('/businesses/switch', [BusinessController::class, 'switch'])->name('business.switch');
    Route::get('/businesses/{id}/edit', [BusinessController::class, 'edit'])->name('business.edit');
    Route::put('/businesses/{id}/update', [BusinessController::class, 'update'])->name('business.update');
    Route::delete('/businesses/{id}/delete', [BusinessController::class, 'destroy'])->name('business.destroy');

    // Journal entries
    Route::get('/journal-entries', [JournalEntryController::class, 'index'])->name('journal.index');
    Route::post('/journal-entries', [JournalEntryController::class, 'store'])->name('journal.store');
    Route::get('/journal-entries/{id}', [JournalEntryController::class, 'show'])->name('journal.show');
    Route::get('/journal-entries/{id}/edit', [JournalEntryController::class, 'edit'])->name('journal.edit');
    Route::put('/journal-entries/{id}', [JournalEntryController::class, 'update'])->name('journal.update');
    Route::delete('/journal-entries/{id}', [JournalEntryController::class, 'destroy'])->name('journal.destroy');

    // Accounting periods
    Route::get('/accounting-periods', [AccountingPeriodController::class, 'index'])->name('accountingperiod.index');
    Route::post('/accounting-periods', [AccountingPeriodController::class, 'store'])->name('accountingperiod.store');
    Route::get('/accounting-periods/{id}/edit', [AccountingPeriodController::class, 'edit'])->name('accountingperiod.edit');
    Route::put('/accounting-periods/{id}', [AccountingPeriodController::class, 'update'])->name('accountingperiod.update');
    Route::delete('/accounting-periods/{id}', [AccountingPeriodController::class, 'destroy'])->name('accountingperiod.destroy');

    // Customers
    Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}/edit', [\App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');

    // Customer invoices
    Route::get('/customer-invoices', [\App\Http\Controllers\CustomerInvoiceController::class, 'index'])->name('customerinvoices.index');
    Route::post('/customer-invoices', [\App\Http\Controllers\CustomerInvoiceController::class, 'store'])->name('customerinvoices.store');
    Route::get('/customer-invoices/{id}', [\App\Http\Controllers\CustomerInvoiceController::class, 'show'])->name('customerinvoices.show');
    Route::get('/customer-invoices/{id}/edit', [\App\Http\Controllers\CustomerInvoiceController::class, 'edit'])->name('customerinvoices.edit');
    Route::put('/customer-invoices/{id}', [\App\Http\Controllers\CustomerInvoiceController::class, 'update'])->name('customerinvoices.update');
    Route::delete('/customer-invoices/{id}', [\App\Http\Controllers\CustomerInvoiceController::class, 'destroy'])->name('customerinvoices.destroy');
    Route::get('/customers/{id}/invoices', [\App\Http\Controllers\CustomerInvoiceController::class, 'indexByCustomer'])->name('customerinvoices.bycustomer');

    // Customer credit notes
    Route::get('/customer-credit-notes', [\App\Http\Controllers\CustomerCreditNoteController::class, 'index'])->name('customercreditnotes.index');
    Route::post('/customer-credit-notes', [\App\Http\Controllers\CustomerCreditNoteController::class, 'store'])->name('customercreditnotes.store');
    Route::get('/customer-credit-notes/{id}', [\App\Http\Controllers\CustomerCreditNoteController::class, 'show'])->name('customercreditnotes.show');
    Route::get('/customer-credit-notes/{id}/edit', [\App\Http\Controllers\CustomerCreditNoteController::class, 'edit'])->name('customercreditnotes.edit');
    Route::put('/customer-credit-notes/{id}', [\App\Http\Controllers\CustomerCreditNoteController::class, 'update'])->name('customercreditnotes.update');
    Route::delete('/customer-credit-notes/{id}', [\App\Http\Controllers\CustomerCreditNoteController::class, 'destroy'])->name('customercreditnotes.destroy');
    // Payments
    Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [\App\Http\Controllers\PaymentController::class, 'create'])->name('payments.create');
    Route::get('/payments/{id}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{id}/edit', [\App\Http\Controllers\PaymentController::class, 'edit'])->name('payments.edit');
    Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{id}', [\App\Http\Controllers\PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{id}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');

    // Invoice payment allocations
    Route::get('/invoice-payments', [\App\Http\Controllers\InvoicePaymentController::class, 'index'])->name('invoicepayments.index');
    Route::get('/invoice-payments/create', [\App\Http\Controllers\InvoicePaymentController::class, 'create'])->name('invoicepayments.create');
    Route::post('/invoice-payments', [\App\Http\Controllers\InvoicePaymentController::class, 'store'])->name('invoicepayments.store');

    // Supplier bill payment allocations
    Route::get('/supplier-bill-payments', [\App\Http\Controllers\SupplierBillPaymentController::class, 'index'])->name('supplierbillpayments.index');
    Route::get('/supplier-bill-payments/create', [\App\Http\Controllers\SupplierBillPaymentController::class, 'create'])->name('supplierbillpayments.create');
    Route::post('/supplier-bill-payments', [\App\Http\Controllers\SupplierBillPaymentController::class, 'store'])->name('supplierbillpayments.store');

    // Suppliers (AP)
    Route::get('/suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{id}/edit', [\App\Http\Controllers\SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::get('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'show'])->name('suppliers.show');
    Route::put('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/supplier-bills', [\App\Http\Controllers\SupplierBillController::class, 'index'])->name('supplierbills.index');
    Route::post('/supplier-bills', [\App\Http\Controllers\SupplierBillController::class, 'store'])->name('supplierbills.store');

    // Balance Sheet reports
    Route::get('/reports/financial/balance-sheet', [BalanceSheetReportController::class, 'index'])->name('reports.financial.balance-sheet.index');
    Route::get('/reports/financial/balance-sheet/create', [BalanceSheetReportController::class, 'create'])->name('reports.financial.balance-sheet.create');
    Route::post('/reports/financial/balance-sheet', [BalanceSheetReportController::class, 'store'])->name('reports.financial.balance-sheet.store');
    Route::get('/reports/financial/balance-sheet/{report}', [BalanceSheetReportController::class, 'show'])->name('reports.financial.balance-sheet.show');
    Route::get('/reports/financial/balance-sheet/{report}/edit', [BalanceSheetReportController::class, 'edit'])->name('reports.financial.balance-sheet.edit');
    Route::put('/reports/financial/balance-sheet/{report}', [BalanceSheetReportController::class, 'update'])->name('reports.financial.balance-sheet.update');
    Route::get('/supplier-bills/{id}', [\App\Http\Controllers\SupplierBillController::class, 'show'])->name('supplierbills.show');
    Route::get('/supplier-bills/{id}/edit', [\App\Http\Controllers\SupplierBillController::class, 'edit'])->name('supplierbills.edit');
    Route::put('/supplier-bills/{id}', [\App\Http\Controllers\SupplierBillController::class, 'update'])->name('supplierbills.update');
    Route::delete('/supplier-bills/{id}', [\App\Http\Controllers\SupplierBillController::class, 'destroy'])->name('supplierbills.destroy');

    // Cash Flow Statement reports
    Route::get('/reports/financial/cashflow', [\App\Http\Controllers\CashFlowReportController::class, 'index'])->name('reports.financial.cashflow.index');
    Route::get('/reports/financial/cashflow/create', [\App\Http\Controllers\CashFlowReportController::class, 'create'])->name('reports.financial.cashflow.create');
    Route::post('/reports/financial/cashflow', [\App\Http\Controllers\CashFlowReportController::class, 'store'])->name('reports.financial.cashflow.store');
    Route::get('/reports/financial/cashflow/{report}', [\App\Http\Controllers\CashFlowReportController::class, 'show'])->name('reports.financial.cashflow.showSaved');
    Route::get('/reports/financial/cashflow/{report}/edit', [\App\Http\Controllers\CashFlowReportController::class, 'edit'])->name('reports.financial.cashflow.editSaved');
    Route::put('/reports/financial/cashflow/{report}', [\App\Http\Controllers\CashFlowReportController::class, 'update'])->name('reports.financial.cashflow.updateSaved');
    Route::get('/reports/financial/cashflow/{report}/export', [\App\Http\Controllers\CashFlowReportController::class, 'exportSaved'])->name('reports.financial.cashflow.exportSaved');
    Route::get('/reports/financial/cashflow/export', [\App\Http\Controllers\CashFlowReportController::class, 'export'])->name('reports.financial.cashflow.export');

    // Statement of Changes in Equity (SCE) routes
    Route::get('/reports/financial/changes-in-equity', [\App\Http\Controllers\ChangesInEquityReportController::class, 'create'])
        ->name('reports.financial.changes-in-equity');
    Route::get('/reports/financial/changes-in-equity/index', [\App\Http\Controllers\ChangesInEquityReportController::class, 'index'])
        ->name('reports.financial.changes-in-equity.index');
    Route::post('/reports/financial/changes-in-equity', [\App\Http\Controllers\ChangesInEquityReportController::class, 'store'])
        ->name('reports.financial.changes-in-equity.store');
    Route::get('/reports/financial/changes-in-equity/{report}', [\App\Http\Controllers\ChangesInEquityReportController::class, 'show'])
        ->name('reports.financial.changes-in-equity.show');
    Route::get('/reports/financial/changes-in-equity/{report}/edit', [\App\Http\Controllers\ChangesInEquityReportController::class, 'edit'])
        ->name('reports.financial.changes-in-equity.edit');
    Route::put('/reports/financial/changes-in-equity/{report}', [\App\Http\Controllers\ChangesInEquityReportController::class, 'update'])
        ->name('reports.financial.changes-in-equity.update');

    Route::get('/supplier-payments', [\App\Http\Controllers\SupplierPaymentController::class, 'index'])->name('supplierpayments.index');
    Route::post('/supplier-payments', [\App\Http\Controllers\SupplierPaymentController::class, 'store'])->name('supplierpayments.store');
    Route::get('/supplier-payments/{id}', [\App\Http\Controllers\SupplierPaymentController::class, 'show'])->name('supplierpayments.show');
    Route::get('/supplier-payments/{id}/edit', [\App\Http\Controllers\SupplierPaymentController::class, 'edit'])->name('supplierpayments.edit');
    Route::put('/supplier-payments/{id}', [\App\Http\Controllers\SupplierPaymentController::class, 'update'])->name('supplierpayments.update');
    Route::delete('/supplier-payments/{id}', [\App\Http\Controllers\SupplierPaymentController::class, 'destroy'])->name('supplierpayments.destroy');

    Route::get('/supplier-credit-notes', [\App\Http\Controllers\SupplierCreditNoteController::class, 'index'])->name('suppliercreditnotes.index');
    Route::post('/supplier-credit-notes', [\App\Http\Controllers\SupplierCreditNoteController::class, 'store'])->name('suppliercreditnotes.store');
    Route::get('/supplier-credit-notes/{id}', [\App\Http\Controllers\SupplierCreditNoteController::class, 'show'])->name('suppliercreditnotes.show');
    Route::get('/supplier-credit-notes/{id}/edit', [\App\Http\Controllers\SupplierCreditNoteController::class, 'edit'])->name('suppliercreditnotes.edit');
    Route::put('/supplier-credit-notes/{id}', [\App\Http\Controllers\SupplierCreditNoteController::class, 'update'])->name('suppliercreditnotes.update');
    Route::delete('/supplier-credit-notes/{id}', [\App\Http\Controllers\SupplierCreditNoteController::class, 'destroy'])->name('suppliercreditnotes.destroy');

    Route::get('/supplier-debit-notes', [\App\Http\Controllers\SupplierDebitNoteController::class, 'index'])->name('supplierdebitnotes.index');
    Route::post('/supplier-debit-notes', [\App\Http\Controllers\SupplierDebitNoteController::class, 'store'])->name('supplierdebitnotes.store');
    Route::get('/supplier-debit-notes/{id}', [\App\Http\Controllers\SupplierDebitNoteController::class, 'show'])->name('supplierdebitnotes.show');
    Route::get('/supplier-debit-notes/{id}/edit', [\App\Http\Controllers\SupplierDebitNoteController::class, 'edit'])->name('supplierdebitnotes.edit');
    Route::put('/supplier-debit-notes/{id}', [\App\Http\Controllers\SupplierDebitNoteController::class, 'update'])->name('supplierdebitnotes.update');
    Route::delete('/supplier-debit-notes/{id}', [\App\Http\Controllers\SupplierDebitNoteController::class, 'destroy'])->name('supplierdebitnotes.destroy');

    // Reports index
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    // Financial Statements: Profit & Loss (index route name used by views)
    Route::get('/reports/financial-statements/profit-and-loss', [ProfitAndLossReportController::class, 'index'])
        ->name('reports.financialStatements.profit-and-loss.index');

    // Financial Statements: Profit & Loss (Actual vs Budget) index
    Route::get('/reports/financial-statements/profit-and-loss/actual-and-budget', [\App\Http\Controllers\ProfitAndLossActualVsBudgetController::class, 'index'])
        ->name('reports.financialStatements.profit-and-loss.actual-and-budget.index');

    // Profit & Loss Reports CRUD (route names used by views)
    Route::prefix('/reports/financial/profit-and-loss')->group(function () {
        Route::get('/create', [ProfitAndLossReportController::class, 'create'])->name('reports.financial.profit-and-loss.create');
        Route::post('/', [ProfitAndLossReportController::class, 'store'])->name('reports.financial.profit-and-loss.store');
        Route::get('/{report}', [ProfitAndLossReportController::class, 'show'])->name('reports.financial.profit-and-loss.show');
        Route::get('/{report}/edit', [ProfitAndLossReportController::class, 'edit'])->name('reports.financial.profit-and-loss.edit');
        Route::put('/{report}', [ProfitAndLossReportController::class, 'update'])->name('reports.financial.profit-and-loss.update');
        Route::get('/{report}/export', [ProfitAndLossReportController::class, 'export'])->name('reports.financial.profit-and-loss.export');
        
        // Actual vs Budget routes
        Route::get('/actual-and-budget/create', [\App\Http\Controllers\ProfitAndLossActualVsBudgetController::class, 'create'])->name('reports.financial.profit-and-loss.actual-and-budget.create');
        Route::post('/actual-and-budget', [\App\Http\Controllers\ProfitAndLossActualVsBudgetController::class, 'store'])->name('reports.financial.profit-and-loss.actual-and-budget.store');
        Route::get('/actual-and-budget/{report}', [\App\Http\Controllers\ProfitAndLossActualVsBudgetController::class, 'show'])->name('reports.financial.profit-and-loss.actual-and-budget.show');
        Route::get('/actual-and-budget/{report}/edit', [\App\Http\Controllers\ProfitAndLossActualVsBudgetController::class, 'edit'])->name('reports.financial.profit-and-loss.actual-and-budget.edit');
        Route::put('/actual-and-budget/{report}', [\App\Http\Controllers\ProfitAndLossActualVsBudgetController::class, 'update'])->name('reports.financial.profit-and-loss.actual-and-budget.update');
    });
});
require __DIR__.'/auth.php';
