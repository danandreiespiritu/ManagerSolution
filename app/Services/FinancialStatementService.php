<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class FinancialStatementService
{
    protected GeneralLedgerService $ledger;

    public function __construct(GeneralLedgerService $ledger)
    {
        $this->ledger = $ledger;
    }

    /**
     * Produce Profit & Loss for a period (from..to).
     * Returns grouped PL accounts and totals.
     *
     * @param int|null $businessId
     * @param string|null $from
     * @param string|null $to
     * @return array ['income'=>Collection,'expense'=>Collection,'total_income'=>float,'total_expense'=>float,'net'=>float]
     */
    public function getProfitAndLoss(?int $businessId = null, ?string $from = null, ?string $to = null): array
    {
        $balances = $this->ledger->getAccountBalances($businessId, $from, $to);

        // Find chart_of_accounts types for PL accounts
        $plAccountIds = $balances->pluck('account_id')->filter()->all();
        $acctRows = [];
        if (count($plAccountIds) > 0) {
            $acctRows = DB::table('chart_of_accounts')
                ->whereIn('id', $plAccountIds)
                ->where('account_type', 'PL')
                ->get()
                ->keyBy('id');
        }

        $income = collect();
        $expense = collect();
        foreach ($balances as $b) {
            $acct = $acctRows[$b->account_id] ?? null;
            if (! $acct) continue;

            // classify by name heuristics: sales/revenue => income; expense/purchase/cogs => expense
            $name = strtolower($acct->account_name ?? '');
            if (str_contains($name, 'sale') || str_contains($name, 'revenue') || str_contains($name, 'income')) {
                $income->push((object) array_merge((array)$b, ['account_name' => $acct->account_name, 'account_code' => $acct->account_code]));
            } else {
                $expense->push((object) array_merge((array)$b, ['account_name' => $acct->account_name, 'account_code' => $acct->account_code]));
            }
        }

        $totalIncome = $income->sum('balance');
        $totalExpense = $expense->sum('balance');
        $net = $totalIncome - $totalExpense;

        return [
            'income' => $income->sortByDesc('balance')->values(),
            'expense' => $expense->sortByDesc('balance')->values(),
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'net' => (float) $net,
        ];
    }

    /**
     * Produce a Balance Sheet as of a date (to).
     * Returns Assets, Liabilities, Equity groups and totals.
     *
     * @param int|null $businessId
     * @param string|null $to
     * @return array ['assets'=>Collection,'liabilities'=>Collection,'equity'=>Collection,'total_assets'=>float,'total_liabilities'=>float,'total_equity'=>float]
     */
    public function getBalanceSheet(?int $businessId = null, ?string $to = null): array
    {
        // ledger balances up to 'to' date
        $balances = $this->ledger->getAccountBalances($businessId, null, $to);

        $bsAccountIds = $balances->pluck('account_id')->filter()->all();
        $acctRows = [];
        if (count($bsAccountIds) > 0) {
            $acctRows = DB::table('chart_of_accounts')
                ->whereIn('id', $bsAccountIds)
                ->where('account_type', 'BL')
                ->get()
                ->keyBy('id');
        }

        $assets = collect();
        $liabilities = collect();
        $equity = collect();

        foreach ($balances as $b) {
            $acct = $acctRows[$b->account_id] ?? null;
            if (! $acct) continue;

            // include grouping/classification metadata from chart_of_accounts so callers can group/accounts properly
            $meta = [
                'account_name' => $acct->account_name ?? $acct->name ?? null,
                'account_code' => $acct->account_code ?? null,
                'group' => $acct->group ?? $acct->account_group ?? 'Other',
                'group_category' => $acct->group_category ?? null,
                'classification' => $acct->classification ?? null,
                'account_type' => $acct->account_type ?? null,
            ];

            $name = strtolower($meta['account_name'] ?? '');
            if (str_contains($name, 'asset') || str_contains($name, 'cash') || str_contains($name, 'receiv') || strtolower($meta['group'] ?? '') === 'current assets' || strtolower($meta['group_category'] ?? '') === 'assets') {
                $assets->push((object) array_merge((array)$b, $meta));
            } elseif (str_contains($name, 'liabil') || str_contains($name, 'payabl') || str_contains($name, 'tax') || strtolower($meta['group_category'] ?? '') === 'liabilities') {
                $liabilities->push((object) array_merge((array)$b, $meta));
            } else {
                $equity->push((object) array_merge((array)$b, $meta));
            }
        }

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        return [
            'assets' => $assets->sortByDesc('balance')->values(),
            'liabilities' => $liabilities->sortByDesc('balance')->values(),
            'equity' => $equity->sortByDesc('balance')->values(),
            'total_assets' => (float) $totalAssets,
            'total_liabilities' => (float) $totalLiabilities,
            'total_equity' => (float) $totalEquity,
        ];
    }

    /**
     * Produce a Cash Flow Statement-like structure for a period.
     * The method uses the chart_of_accounts.cash_flow_category field to map accounts
     * into sections (operating/investing/financing) and optional line names.
     *
     * Returned structure is compatible with the views under reports/.../cashflowstatement:
     * [
     *   'sections' => [ 'operating' => ['lines' => [ ['name'=>..,'amount'=>..], ... ], 'total'=>.. ], ... ],
     *   'totals' => ['operating'=>..,'investing'=>..,'financing'=>..],
     *   'netIncrease' => float,
     *   'cashBeginning' => float,
     *   'cashEnding' => float,
     * ]
     *
     * This implementation is intentionally conservative: it aggregates balances for
     * accounts that have a non-empty `cash_flow_category` and groups them by the
     * category prefix (section) and optional line label after a ':' separator.
     */
    public function getCashFlow(?int $businessId = null, ?string $from = null, ?string $to = null, string $method = 'indirect'): array
    {
        // fetch account balances for the period (transactions between from..to)
        $periodBalances = $this->ledger->getAccountBalances($businessId, $from, $to);

        // fetch chart accounts that have a cash_flow_category defined for this business
        $coaQuery = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
            ->whereNotNull('cash_flow_category');
        if ($businessId) $coaQuery->where('business_id', $businessId);
        $coaRows = $coaQuery->get()->keyBy('id');

        // Map account id => cash_flow_category
        $acctMap = [];
        foreach ($coaRows as $id => $r) {
            $acctMap[$id] = $r->cash_flow_category;
        }

        // Aggregate lines per section
        $sections = [
            'operating' => ['lines' => [], 'total' => 0.0],
            'investing' => ['lines' => [], 'total' => 0.0],
            'financing' => ['lines' => [], 'total' => 0.0],
        ];

        // helper to add amount to a named line
        $addLine = function (&$secKey, $lineName, $amount) use (&$sections) {
            if (! isset($sections[$secKey])) {
                $sections[$secKey] = ['lines' => [], 'total' => 0.0];
            }
            // find existing line
            $found = null;
            foreach ($sections[$secKey]['lines'] as &$l) {
                if ($l['name'] === $lineName) { $found = &$l; break; }
            }
            if ($found === null) {
                $sections[$secKey]['lines'][] = ['name' => $lineName, 'amount' => (float)$amount];
            } else {
                $found['amount'] = (float)$found['amount'] + (float)$amount;
            }
            $sections[$secKey]['total'] = (float) array_sum(array_column($sections[$secKey]['lines'], 'amount'));
        };

        // For each balance row, if account has cash_flow_category, map it
        foreach ($periodBalances as $b) {
            $acctId = $b->account_id ?? null;
            if (! $acctId) continue;
            $cf = $acctMap[$acctId] ?? null;
            if (empty($cf)) continue;
            // category might be like 'operating:Depreciation' or 'investing'
            $parts = array_map('trim', explode(':', $cf, 2));
            $sec = strtolower($parts[0] ?? 'operating');
            if (! in_array($sec, ['operating','investing','financing'])) {
                // fall back: try keywords
                if (str_contains($sec, 'invest')) $sec = 'investing';
                elseif (str_contains($sec, 'financ')) $sec = 'financing';
                else $sec = 'operating';
            }
            $line = $parts[1] ?? ($b->account_name ?? 'Other');

            // amount: use the ledger balance for the period. Keep sign as computed by ledger
            $amt = (float) ($b->balance ?? 0.0);

            // For indirect method, many PL non-cash items will be recorded as expenses (negative balances)
            // but an expense (debit) should be added back when converting to cash: the signage and
            // how account balances are stored depends on accounting conventions in this app. We will
            // preserve the ledger balance and let the view show negatives in parentheses.

            $addLine($sec, $line, $amt);
        }

        // totals
        $totals = [
            'operating' => $sections['operating']['total'] ?? 0.0,
            'investing' => $sections['investing']['total'] ?? 0.0,
            'financing' => $sections['financing']['total'] ?? 0.0,
        ];

        // cash at beginning: balances for 'cash' accounts up to day before 'from'
        $cashBeginning = 0.0;
        try {
            $before = null;
            if ($from) {
                $dt = new \DateTime($from);
                $dt->modify('-1 day');
                $before = $dt->format('Y-m-d');
            }
            $cashBalancesBefore = $this->ledger->getAccountBalances($businessId, null, $before);
            foreach ($cashBalancesBefore as $cb) {
                $name = strtolower($cb->account_name ?? '');
                if (str_contains($name, 'cash') || str_contains($name, 'bank')) {
                    $cashBeginning += (float)$cb->balance;
                }
            }
        } catch (\Throwable $e) {
            $cashBeginning = 0.0;
        }

        // cash at end: balances for 'cash' accounts up to 'to'
        $cashEnding = 0.0;
        try {
            $cashBalancesEnd = $this->ledger->getAccountBalances($businessId, null, $to);
            foreach ($cashBalancesEnd as $cb) {
                $name = strtolower($cb->account_name ?? '');
                if (str_contains($name, 'cash') || str_contains($name, 'bank')) {
                    $cashEnding += (float)$cb->balance;
                }
            }
        } catch (\Throwable $e) {
            $cashEnding = 0.0;
        }

        $netIncrease = $cashEnding - $cashBeginning;

        // Convert sections lines to numeric amounts and ensure stable ordering
        foreach ($sections as $k => &$s) {
            usort($s['lines'], function ($a, $b) { return strcasecmp($a['name'], $b['name']); });
            $s['total'] = (float) ($s['total'] ?? 0.0);
        }

        return [
            'sections' => $sections,
            'totals' => $totals,
            'netIncrease' => (float) $netIncrease,
            'cashBeginning' => (float) $cashBeginning,
            'cashEnding' => (float) $cashEnding,
        ];
    }
}
