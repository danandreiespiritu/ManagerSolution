<?php

namespace Tests\Feature;

use App\Models\AccountingPeriod;
use App\Models\Business;
use App\Models\ChartofAccounts;
use App\Models\JournalEntry;
use App\Models\Supplier;
use App\Services\SupplierPayablesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierPayablesPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_bill_and_payment_create_balanced_journal_entries(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Test Biz',
        ]);

        // Set current business for BusinessScope
        session(['current_business_id' => $business->id]);

        $period = AccountingPeriod::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'period_name' => 'Dec 2025',
            'start_date' => '2025-12-01',
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
            'account_name' => 'Office Expense',
            'account_type' => 'Expense',
            'group' => 'Expenses',
            'is_active' => true,
        ]);

        $cash = ChartofAccounts::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'account_code' => '1000',
            'account_name' => 'Cash',
            'account_type' => 'Asset',
            'group' => 'Cash',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'supplier_code' => 'SUP-001',
            'supplier_name' => 'Vendor One',
            'email' => 'vendor@example.com',
            'is_active' => true,
        ]);

        /** @var SupplierPayablesService $service */
        $service = app(SupplierPayablesService::class);

        $bill = $service->createBillAndPost($user->id, [
            'bill_number' => 'BILL-100',
            'supplier_id' => $supplier->id,
            'bill_date' => '2025-12-15',
            'due_date' => '2025-12-31',
            'total_amount' => 100.00,
            'expense_account_id' => $expense->id,
            'ap_account_id' => $ap->id,
        ]);

        $this->assertSame('Unpaid', $bill->status);

        $billEntry = JournalEntry::where('reference_type', 'SupplierBill')
            ->where('reference_id', $bill->id)
            ->first();

        $this->assertNotNull($billEntry);
        $this->assertSame('2025-12-15', $billEntry->entry_date->format('Y-m-d'));

        $billLines = $billEntry->lines()->get();
        $this->assertCount(2, $billLines);
        $this->assertEquals(100.00, (float) $billLines->sum('debit_amount'));
        $this->assertEquals(100.00, (float) $billLines->sum('credit_amount'));

        $payment = $service->createPaymentAndPost($user->id, [
            'supplier_id' => $supplier->id,
            'payment_date' => '2025-12-20',
            'amount' => 100.00,
            'payment_method' => 'Bank',
            'reference' => 'TRX-1',
            'cash_account_id' => $cash->id,
            'ap_account_id' => $ap->id,
            'allocations' => [
                ['bill_id' => $bill->id, 'amount' => 100.00],
            ],
        ]);

        $paymentEntry = JournalEntry::where('reference_type', 'SupplierPayment')
            ->where('reference_id', $payment->id)
            ->first();

        $this->assertNotNull($paymentEntry);
        $paymentLines = $paymentEntry->lines()->get();
        $this->assertCount(2, $paymentLines);
        $this->assertEquals(100.00, (float) $paymentLines->sum('debit_amount'));
        $this->assertEquals(100.00, (float) $paymentLines->sum('credit_amount'));

        $bill->refresh();
        $this->assertSame('Paid', $bill->status);
        $this->assertEquals(0.0, (float) $bill->balanceDue());
    }

    public function test_cannot_post_into_closed_period(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $business = Business::create([
            'user_id' => $user->id,
            'business_name' => 'Test Biz',
        ]);
        session(['current_business_id' => $business->id]);

        AccountingPeriod::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'period_name' => 'Dec 2025',
            'start_date' => '2025-12-01',
            'end_date' => '2025-12-31',
            'is_closed' => true,
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
            'account_name' => 'Office Expense',
            'account_type' => 'Expense',
            'group' => 'Expenses',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'supplier_code' => 'SUP-001',
            'supplier_name' => 'Vendor One',
            'is_active' => true,
        ]);

        $service = app(SupplierPayablesService::class);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $service->createBillAndPost($user->id, [
            'bill_number' => 'BILL-100',
            'supplier_id' => $supplier->id,
            'bill_date' => '2025-12-15',
            'due_date' => '2025-12-31',
            'total_amount' => 100.00,
            'expense_account_id' => $expense->id,
            'ap_account_id' => $ap->id,
        ]);
    }
}
