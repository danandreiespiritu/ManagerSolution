<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('supplier_credit_notes')) {
            Schema::create('supplier_credit_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('business_id')->nullable()->constrained('businesses')->cascadeOnDelete();

                $table->string('credit_note_number')->index();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->date('credit_date');
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->text('reason')->nullable();
                $table->string('status')->default('Open');

                // Optional explicit posting accounts
                $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->foreignId('offset_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();

                $table->timestamps();
            });
        }

        if (! Schema::hasTable('supplier_payments')) {
            Schema::create('supplier_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('business_id')->nullable()->constrained('businesses')->cascadeOnDelete();

                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->date('payment_date');
                $table->decimal('amount', 15, 2);
                $table->string('payment_method', 50)->default('Cash');
                $table->string('reference')->nullable();

                // Accounts for posting
                $table->foreignId('cash_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
                $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();

                $table->string('status')->default('Posted');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('supplier_bill_payments')) {
            Schema::create('supplier_bill_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('business_id')->nullable()->constrained('businesses')->cascadeOnDelete();

                $table->foreignId('supplier_payment_id')->constrained('supplier_payments')->cascadeOnDelete();
                $table->foreignId('supplier_bill_id')->constrained('supplier_bills')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);

                $table->timestamps();

                // MySQL has a max identifier length; keep index name short.
                $table->index(['supplier_payment_id', 'supplier_bill_id'], 'sbp_payment_bill_idx');
            });
        }

        Schema::table('supplier_bills', function (Blueprint $table) {
            // Optional explicit posting accounts
            if (!Schema::hasColumn('supplier_bills', 'expense_account_id')) {
                $table->foreignId('expense_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            }
            if (!Schema::hasColumn('supplier_bills', 'ap_account_id')) {
                $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            }
        });

        Schema::table('supplier_debit_notes', function (Blueprint $table) {
            // Optional explicit posting accounts
            if (!Schema::hasColumn('supplier_debit_notes', 'expense_account_id')) {
                $table->foreignId('expense_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            }
            if (!Schema::hasColumn('supplier_debit_notes', 'ap_account_id')) {
                $table->foreignId('ap_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            }
            if (!Schema::hasColumn('supplier_debit_notes', 'status')) {
                $table->string('status')->default('Open');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_debit_notes', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_debit_notes', 'expense_account_id')) {
                $table->dropConstrainedForeignId('expense_account_id');
            }
            if (Schema::hasColumn('supplier_debit_notes', 'ap_account_id')) {
                $table->dropConstrainedForeignId('ap_account_id');
            }
            if (Schema::hasColumn('supplier_debit_notes', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('supplier_bills', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_bills', 'expense_account_id')) {
                $table->dropConstrainedForeignId('expense_account_id');
            }
            if (Schema::hasColumn('supplier_bills', 'ap_account_id')) {
                $table->dropConstrainedForeignId('ap_account_id');
            }
        });

        Schema::dropIfExists('supplier_bill_payments');
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_credit_notes');
    }
};
