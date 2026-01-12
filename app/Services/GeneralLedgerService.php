<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class GeneralLedgerService
{
    /**
     * Return account balances grouped by account_id.
     *
     * @param int|null $businessId
     * @param string|null $from (Y-m-d)
     * @param string|null $to (Y-m-d)
     * @return Collection
     */
    public function getAccountBalances(?int $businessId = null, ?string $from = null, ?string $to = null): Collection
    {
        $query = DB::table('journal_entry_lines as l')
            ->select(
                'l.account_id',
                DB::raw('COALESCE(SUM(l.debit_amount),0) as total_debit'),
                DB::raw('COALESCE(SUM(l.credit_amount),0) as total_credit'),
                DB::raw('COALESCE(SUM(l.debit_amount - l.credit_amount),0) as balance')
            )
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->when($from, fn($q) => $q->where('e.entry_date', '>=', $from))
            ->when($to, fn($q) => $q->where('e.entry_date', '<=', $to))
            ->groupBy('l.account_id');

        $rows = $query->get();

        $accountIds = $rows->pluck('account_id')->filter()->unique()->values()->all();

        $accounts = [];
        if (count($accountIds) > 0) {
            $acctRows = DB::table('chart_of_accounts')
                ->whereIn('id', $accountIds)
                ->get()
                ->keyBy('id');

            foreach ($acctRows as $id => $r) {
                $accounts[$id] = $r;
            }
        }

        return $rows->map(function ($r) use ($accounts) {
            $acct = $accounts[$r->account_id] ?? null;
            return (object) [
                'account_id' => $r->account_id,
                'account_name' => $acct->account_name ?? null,
                'account_code' => $acct->account_code ?? null,
                'total_debit' => (float) $r->total_debit,
                'total_credit' => (float) $r->total_credit,
                'balance' => (float) $r->balance,
            ];
        });
    }

    /**
     * Return trial balance totals for the given filters.
     *
     * @param int|null $businessId
     * @param string|null $from
     * @param string|null $to
     * @return array ['total_debit'=>float,'total_credit'=>float,'accounts'=>Collection]
     */
    public function getTrialBalance(?int $businessId = null, ?string $from = null, ?string $to = null): array
    {
        $accounts = $this->getAccountBalances($businessId, $from, $to);
        $totalDebit = $accounts->sum('total_debit');
        $totalCredit = $accounts->sum('total_credit');

        return [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'accounts' => $accounts,
        ];
    }
}
