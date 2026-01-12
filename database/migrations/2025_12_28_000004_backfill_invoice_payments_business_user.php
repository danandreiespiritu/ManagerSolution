<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Backfill user_id and business_id on invoice_payments in a DB-agnostic way.
        $rows = DB::table('invoice_payments')
            ->whereNull('user_id')
            ->orWhereNull('business_id')
            ->get();

        foreach ($rows as $row) {
            $updates = [];

            // Try to populate from payments if available
            if (! empty($row->payment_id)) {
                $p = DB::table('payments')->where('id', $row->payment_id)->first();

                if ($p) {
                    if (is_null($row->user_id) && isset($p->user_id)) {
                        $updates['user_id'] = $p->user_id;
                    }
                    if (is_null($row->business_id) && isset($p->business_id)) {
                        $updates['business_id'] = $p->business_id;
                    }
                }
            }

            // If still missing business_id, try customer_invoices
            if ((empty($updates['business_id'])) && ! empty($row->customer_invoice_id)) {
                $ci = DB::table('customer_invoices')->where('id', $row->customer_invoice_id)->first();
                if ($ci && isset($ci->business_id)) {
                    $updates['business_id'] = $ci->business_id;
                }
            }

            if (! empty($updates)) {
                DB::table('invoice_payments')->where('id', $row->id)->update($updates);
            }
        }
    }

    public function down()
    {
        // Do not attempt to undo backfill
    }
};
