<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Only create views when the underlying tables exist to avoid migration ordering errors
        if (! Schema::hasTable('journal_entries') || ! Schema::hasTable('journal_entry_lines')) {
            // underlying tables not present yet; skip view creation
            return;
        }

        // GeneralLedger view
        DB::statement(<<<'SQL'
CREATE OR REPLACE VIEW general_ledger AS
SELECT
  je.user_id,
  je.entry_date,
  jel.account_id,
  jel.debit_amount,
  jel.credit_amount,
  SUM(jel.debit_amount - jel.credit_amount) OVER (PARTITION BY je.user_id, jel.account_id ORDER BY je.entry_date, je.id, jel.id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS running_balance
FROM journal_entries je
JOIN journal_entry_lines jel ON jel.journal_entry_id = je.id;
SQL
        );

        // TrialBalance view
        DB::statement(<<<'SQL'
CREATE OR REPLACE VIEW trial_balance AS
SELECT
  je.user_id,
  jel.account_id,
  SUM(jel.debit_amount) AS total_debit,
  SUM(jel.credit_amount) AS total_credit,
  SUM(jel.debit_amount - jel.credit_amount) AS balance
FROM journal_entries je
JOIN journal_entry_lines jel ON jel.journal_entry_id = je.id
GROUP BY je.user_id, jel.account_id;
SQL
        );
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS general_ledger');
        DB::statement('DROP VIEW IF EXISTS trial_balance');
    }
};
