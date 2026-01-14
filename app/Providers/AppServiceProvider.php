<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\BlsAccountandGroup\IBlsAccountRepository;
use App\Repositories\BlsAccountandGroup\BlsAccountRepository;
use App\Repositories\Business\IBusinessRepository;
use App\Repositories\Business\BusinessRepository;
use App\Observers\BusinessObserver;
use App\Observers\TransactionJournalObserver;
use App\Models\Business;
use App\Models\CustomerInvoice;
use App\Models\SupplierBill;
use App\Models\Payment;
use App\Models\CustomerCreditNote;
use App\Models\SupplierDebitNote;
use App\Repositories\ProfitAndLossReport\IProfitAndLossReportRepository;
use App\Repositories\ProfitAndLossReport\ProfitAndLossReportRepository;
use App\Repositories\TrialBalanceReport\ITrialBalanceReportRepository;
use App\Repositories\TrialBalanceReport\TrialBalanceReportRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IBlsAccountRepository::class, BlsAccountRepository::class);
        $this->app->bind(
            \App\Repositories\PlAccountandGroup\IPlAccountRepository::class,
            \App\Repositories\PlAccountandGroup\PlAccountRepository::class
        );
        $this->app->bind(IBusinessRepository::class, BusinessRepository::class);
        // Customer repositories
        $this->app->bind(\App\Repositories\Customer\ICustomerRepository::class, \App\Repositories\Customer\CustomerRepository::class);
        $this->app->bind(\App\Repositories\CustomerInvoice\ICustomerInvoiceRepository::class, \App\Repositories\CustomerInvoice\CustomerInvoiceRepository::class);
        $this->app->bind(\App\Repositories\CustomerCreditNote\ICustomerCreditNoteRepository::class, \App\Repositories\CustomerCreditNote\CustomerCreditNoteRepository::class);
        // Payment repository
        $this->app->bind(\App\Repositories\Payment\IPaymentRepository::class, \App\Repositories\Payment\PaymentRepository::class);
        // Journal entry repository
        $this->app->bind(
            \App\Repositories\JournalEntry\IJournalEntryRepository::class,
            \App\Repositories\JournalEntry\JournalEntryRepository::class
        );
        // Accounting period repository
        $this->app->bind(
            \App\Repositories\AccountingPeriod\IAccountingPeriodRepository::class,
            \App\Repositories\AccountingPeriod\AccountingPeriodRepository::class
        );

        // Supplier (AP) repositories
        $this->app->bind(\App\Repositories\Supplier\ISupplierRepository::class, \App\Repositories\Supplier\SupplierRepository::class);
        $this->app->bind(\App\Repositories\SupplierBill\ISupplierBillRepository::class, \App\Repositories\SupplierBill\SupplierBillRepository::class);
        $this->app->bind(\App\Repositories\SupplierPayment\ISupplierPaymentRepository::class, \App\Repositories\SupplierPayment\SupplierPaymentRepository::class);
        $this->app->bind(\App\Repositories\SupplierCreditNote\ISupplierCreditNoteRepository::class, \App\Repositories\SupplierCreditNote\SupplierCreditNoteRepository::class);
        $this->app->bind(\App\Repositories\SupplierDebitNote\ISupplierDebitNoteRepository::class, \App\Repositories\SupplierDebitNote\SupplierDebitNoteRepository::class);

        // Reporting repositories
        $this->app->bind(\App\Repositories\ReportDefinition\IReportDefinitionRepository::class, \App\Repositories\ReportDefinition\ReportDefinitionRepository::class);
        $this->app->bind(\App\Repositories\ReportFilter\IReportFilterRepository::class, \App\Repositories\ReportFilter\ReportFilterRepository::class);

        // Profit & Loss report repository
        $this->app->bind(IProfitAndLossReportRepository::class, ProfitAndLossReportRepository::class);

        // Trial Balance report repository
        $this->app->bind(ITrialBalanceReportRepository::class, TrialBalanceReportRepository::class);

        // General Ledger Summary report repository
        $this->app->bind(
            \App\Repositories\GeneralLedgerSummaryReport\IGeneralLedgerSummaryReportRepository::class,
            \App\Repositories\GeneralLedgerSummaryReport\GeneralLedgerSummaryReportRepository::class
        );

        // Chart of Accounts repository
        $this->app->bind(\App\Repositories\ChartofAccounts\IChartofAccountsRepository::class, \App\Repositories\ChartofAccounts\ChartofAccountsRepository::class);

        // Posting rules registry and implementations
        $this->app->singleton(\App\Services\PostingRules\PostingRuleRegistry::class, function ($app) {
            $r = new \App\Services\PostingRules\PostingRuleRegistry();
            $r->register(new \App\Services\PostingRules\APPostingRule());
            $r->register(new \App\Services\PostingRules\ARPostingRule());
            $r->register(new \App\Services\PostingRules\GeneralPostingRule());
            return $r;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Push the SetCurrentBusiness middleware into the web group so
        // the current business is available on every web request.
        if ($this->app->bound(\Illuminate\Routing\Router::class)) {
            $router = $this->app->make(\Illuminate\Routing\Router::class);
            // Ensure web requests load the current business into the container
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\SetCurrentBusiness::class);
            // Also apply to API group so authenticated API requests are scoped
            $router->pushMiddlewareToGroup('api', \App\Http\Middleware\SetCurrentBusiness::class);
        }

        // Register model observers for automatic journal entries
        Business::observe(BusinessObserver::class);
        CustomerInvoice::observe(TransactionJournalObserver::class);
        SupplierBill::observe(TransactionJournalObserver::class);
        Payment::observe(TransactionJournalObserver::class);
        CustomerCreditNote::observe(TransactionJournalObserver::class);
        SupplierDebitNote::observe(TransactionJournalObserver::class);
    }
}