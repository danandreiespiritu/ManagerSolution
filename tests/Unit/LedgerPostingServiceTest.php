<?php

namespace Tests\Unit;

use App\Models\AccountingPeriod;
use App\Models\Business;
use App\Models\ChartofAccounts;
use App\Models\JournalEntry;
use App\Models\JournalEntryAudit;
use App\Models\Supplier;
use App\Services\LedgerPostingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LedgerPostingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reverse_creates_reversal_and_audit(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $business = Business::create(['user_id' => $user->id, 'business_name' => 'B']);
        session(['current_business_id' => $business->id]);

        $period = AccountingPeriod::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'period_name' => 'Jan',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'is_closed' => false,
        ]);

        $ap = ChartofAccounts::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'account_code' => '2000',
            'account_name' => 'Accounts Payable',
            'account_type' => 'Liability',
            'group' => 'Accounts Payable',
            'is_active' => true,
        ]);

        $expense = ChartofAccounts::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'account_code' => '5000',
            'account_name' => 'Expense',
            'account_type' => 'Expense',
            'group' => 'Expenses',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'supplier_name' => 'S',
        ]);

        $service = app(LedgerPostingService::class);

        $entry = $service->post([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'entry_date' => '2025-06-01',
            'reference_type' => 'Test',
            'reference_id' => 1,
            'description' => 'Test posting',
            'accounting_period_id' => $period->id,
            'created_by' => $user->id,
            'lines' => [
                ['account_id' => $expense->id, 'debit_amount' => 50.00],
                ['account_id' => $ap->id, 'credit_amount' => 50.00, 'supplier_id' => $supplier->id],
            ],
        ]);

        $this->assertInstanceOf(JournalEntry::class, $entry);

        // reverse
        $reversal = $service->reverse($entry->id, $user->id, 'Testing reversal');

        $this->assertInstanceOf(JournalEntry::class, $reversal);
        $this->assertDatabaseHas('journal_entry_audits', ['journal_entry_id' => $entry->id, 'action' => 'marked_reversed']);
        $this->assertDatabaseHas('journal_entry_audits', ['journal_entry_id' => $reversal->id, 'action' => 'reversal_posted']);
    }
}
