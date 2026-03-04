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
            ->whereNotNull('l.account_id')
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->when($from, fn($q) => $q->where('e.entry_date', '>=', $from))
            ->when($to, fn($q) => $q->where('e.entry_date', '<=', $to))
            ->groupBy('l.account_id');

        $rows = $query->get();

        $accountIds = $rows->pluck('account_id')->filter()->unique()->values()->all();

        $accounts = [];
        $journalDescriptions = [];
        if (count($accountIds) > 0) {
            $acctRows = DB::table('chart_of_accounts')
                ->whereIn('id', $accountIds)
                ->get()
                ->keyBy('id');

            foreach ($acctRows as $id => $r) {
                $accounts[$id] = $r;
            }

            $descRows = DB::table('journal_entry_lines as l')
                ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                ->whereIn('l.account_id', $accountIds)
                ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
                ->when($from, fn($q) => $q->where('e.entry_date', '>=', $from))
                ->when($to, fn($q) => $q->where('e.entry_date', '<=', $to))
                ->select('l.account_id', 'l.description as line_description', 'e.description as entry_description', 'e.entry_date', 'l.id')
                ->orderByDesc('e.entry_date')
                ->orderByDesc('l.id')
                ->get();

            foreach ($descRows as $dr) {
                $accId = $dr->account_id;
                if (array_key_exists($accId, $journalDescriptions)) {
                    continue;
                }

                $desc = trim((string) ($dr->line_description ?? ''));
                if ($desc === '') {
                    $desc = trim((string) ($dr->entry_description ?? ''));
                }
                $journalDescriptions[$accId] = $desc !== '' ? $desc : null;
            }
        }

        return $rows->map(function ($r) use ($accounts, $journalDescriptions) {
            $acct = $accounts[$r->account_id] ?? null;
            return (object) [
                'account_id' => $r->account_id,
                'account_name' => $acct->account_name ?? null,
                'account_code' => $acct->account_code ?? null,
                'journal_description' => $journalDescriptions[$r->account_id] ?? null,
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

    /**
     * Return general ledger summary per account including beginning balance, period totals and lines.
     *
     * @param int|null $businessId
     * @param string|null $from
     * @param string|null $to
     * @param bool $excludeZero
     * @return \Illuminate\Support\Collection
     */
    public function getGeneralLedgerSummary(?int $businessId = null, ?string $from = null, ?string $to = null, bool $excludeZero = false)
    {
        // load accounts for the business
        $accountsQuery = DB::table('chart_of_accounts')->when($businessId, fn($q) => $q->where('business_id', $businessId))->orderBy('account_code');
        $acctRows = $accountsQuery->get();

        $results = collect();

        foreach ($acctRows as $acct) {
            $accountId = $acct->id;

            // beginning balance: entries before from date
            $beginning = 0.0;
            if ($from) {
                $beginning = (float) DB::table('journal_entry_lines as l')
                    ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                    ->where('l.account_id', $accountId)
                    ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
                    ->where('e.entry_date', '<', $from)
                    ->select(DB::raw('COALESCE(SUM(l.debit_amount - l.credit_amount),0) as bal'))
                    ->value('bal');
            }

            // period totals and lines
            $periodQuery = DB::table('journal_entry_lines as l')
                ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                ->where('l.account_id', $accountId)
                ->when($businessId, fn($q) => $q->where('l.business_id', $businessId));

            if ($from) {
                $periodQuery->where('e.entry_date', '>=', $from);
            }
            if ($to) {
                $periodQuery->where('e.entry_date', '<=', $to);
            }

            $periodLines = $periodQuery->select('l.*', 'e.entry_date', 'e.description as entry_description')
                ->orderBy('e.entry_date')
                ->orderBy('l.id')
                ->get();

            $totalDebit = (float) $periodLines->sum('debit_amount');
            $totalCredit = (float) $periodLines->sum('credit_amount');

            $running = $beginning;
            $lines = $periodLines->map(function ($l) use (&$running) {
                $debit = (float) ($l->debit_amount ?? 0);
                $credit = (float) ($l->credit_amount ?? 0);
                $running = $running + $debit - $credit;

                return (object) [
                    'date' => $l->entry_date ? date('Y-m-d', strtotime($l->entry_date)) : null,
                    'explanation' => $l->description ?? $l->entry_description ?? null,
                    'ref' => $l->journal_entry_id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $running,
                ];
            });

            $ending = $beginning + $totalDebit - $totalCredit;

            if ($excludeZero && abs($beginning) < 0.005 && abs($totalDebit) < 0.005 && abs($totalCredit) < 0.005) {
                continue;
            }

            $results->push((object) [
                'account_id' => $accountId,
                // provide backward-compatible aliases expected by views
                'account_name' => $acct->account_name ?? null,
                'account_code' => $acct->account_code ?? null,
                'name' => $acct->account_name ?? null,
                'code' => $acct->account_code ?? null,
                'classification' => $acct->classification ?? null,
                'beginning_balance' => (float) $beginning,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'net_movement' => $totalDebit - $totalCredit,
                'ending_balance' => (float) $ending,
                'lines' => $lines,
            ]);
        }

        return $results;
    }
}
