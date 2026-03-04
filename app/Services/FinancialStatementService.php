<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            $groupLower = strtolower($meta['group'] ?? '');
            $groupCatLower = strtolower($meta['group_category'] ?? '');

            // Classify using account name, group name or group category containing asset/liability keywords
            if (str_contains($name, 'asset') || str_contains($name, 'cash') || str_contains($name, 'receiv') || str_contains($groupLower, 'asset') || str_contains($groupCatLower, 'asset') || $groupLower === 'current assets') {
                $assets->push((object) array_merge((array)$b, $meta));
            } elseif (str_contains($name, 'liabil') || str_contains($name, 'payabl') || str_contains($name, 'tax') || str_contains($groupLower, 'liab') || str_contains($groupCatLower, 'liab') || $groupCatLower === 'liabilities') {
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
    public function getCashFlow(?int $businessId = null, ?string $from = null, ?string $to = null, string $method = 'indirect', array $overrides = []): array
    {
        // Build a map of chart accounts (for cash_flow_category lookups)
        $coaQuery = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', 1);
        $coaRows = $coaQuery->get()->keyBy('id');

        // Determine cash accounts: explicit cash & cash equivalents or name contains 'cash'/'bank'
        $cashQuery = \Illuminate\Support\Facades\DB::table('chart_of_accounts')
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereRaw("LOWER(COALESCE(cash_flow_category,'')) LIKE '%cash%'")
                  ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%cash%'")
                  ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%bank%'");
            });
        $cashAcctRows = $cashQuery->get()->keyBy('id');
        $cashAccountIds = array_map('intval', array_keys($cashAcctRows->toArray()));

        if (empty($cashAccountIds)) {
            return [
                'sections' => [
                    'operating' => ['lines' => [], 'total' => 0.0],
                    'investing' => ['lines' => [], 'total' => 0.0],
                    'financing' => ['lines' => [], 'total' => 0.0],
                ],
                'totals' => ['operating' => 0.0,'investing' => 0.0,'financing' => 0.0],
                'netIncrease' => 0.0,
                'cashBeginning' => 0.0,
                'cashEnding' => 0.0,
            ];
        }

        // If overrides provided, allow the user to restrict included accounts (respect explicit overrides)
        $overrideIds = [];
        foreach ($overrides as $ov) {
            $acctId = (int) ($ov['account_id'] ?? 0);
            if ($acctId > 0) $overrideIds[] = $acctId;
        }
        $overrideIds = array_values(array_unique($overrideIds));

        // Build the set of journal entries that involve cash accounts within the period
        $entryIdsQuery = \Illuminate\Support\Facades\DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->whereIn('l.account_id', $cashAccountIds);
        if ($from) $entryIdsQuery->where('e.entry_date', '>=', $from);
        if ($to) $entryIdsQuery->where('e.entry_date', '<=', $to);
        $entryIds = $entryIdsQuery->distinct()->pluck('l.journal_entry_id')->all();

        // If no journal entries involve cash accounts, return empty structure
        if (empty($entryIds)) {
            return [
                'sections' => [
                    'operating' => ['lines' => [], 'total' => 0.0],
                    'investing' => ['lines' => [], 'total' => 0.0],
                    'financing' => ['lines' => [], 'total' => 0.0],
                ],
                'totals' => ['operating' => 0.0,'investing' => 0.0,'financing' => 0.0],
                'netIncrease' => 0.0,
                'cashBeginning' => 0.0,
                'cashEnding' => 0.0,
            ];
        }

        // helper to add amount to a named line inside section
        $sections = [
            'operating' => ['lines' => [], 'total' => 0.0],
            'investing' => ['lines' => [], 'total' => 0.0],
            'financing' => ['lines' => [], 'total' => 0.0],
        ];
        $addLine = function (&$secKey, $lineName, $amount) use (&$sections) {
            if (! isset($sections[$secKey])) $sections[$secKey] = ['lines' => [], 'total' => 0.0];
            foreach ($sections[$secKey]['lines'] as &$l) {
                if ($l['name'] === $lineName) { $l['amount'] = (float)$l['amount'] + (float)$amount; $sections[$secKey]['total'] = (float) array_sum(array_column($sections[$secKey]['lines'], 'amount')); return; }
            }
            $sections[$secKey]['lines'][] = ['name' => $lineName, 'amount' => (float)$amount];
            $sections[$secKey]['total'] = (float) array_sum(array_column($sections[$secKey]['lines'], 'amount'));
        };

        $mapSection = static function (?string $raw): string {
            $val = strtolower(trim((string) $raw));
            if ($val === '') return 'operating';
            if (str_contains($val, 'invest')) return 'investing';
            if (str_contains($val, 'financ')) return 'financing';
            if (str_contains($val, 'operat')) return 'operating';

            $parts = array_map('trim', explode(':', $val, 2));
            $head = $parts[0] ?? '';
            if (str_contains($head, 'invest')) return 'investing';
            if (str_contains($head, 'financ')) return 'financing';
            return 'operating';
        };

        // Use actual cash journal lines so report values match journal entry line amounts.
        $hasLineCashCategory = Schema::hasColumn('journal_entry_lines', 'cash_category');
        $selectedCashAccountIds = array_values(array_intersect($cashAccountIds, $overrideIds));

        $cashLinesQuery = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->leftJoin('chart_of_accounts as a', 'a.id', '=', 'l.account_id')
            ->whereIn('l.journal_entry_id', $entryIds)
            ->whereIn('l.account_id', $cashAccountIds)
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->when(count($selectedCashAccountIds) > 0, fn($q) => $q->whereIn('l.account_id', $selectedCashAccountIds))
            ->orderBy('e.entry_date')
            ->orderBy('l.id')
            ->select(
                'l.account_id',
                'l.debit_amount',
                'l.credit_amount',
                'l.description as line_description',
                'e.description as entry_description',
                'a.account_name',
                'a.cash_flow_category as account_cash_flow_category'
            );

        if ($hasLineCashCategory) {
            $cashLinesQuery->addSelect('l.cash_category as line_cash_category');
        } else {
            $cashLinesQuery->addSelect(DB::raw('NULL as line_cash_category'));
        }

        $cashLines = $cashLinesQuery->get();

        foreach ($cashLines as $line) {
            $amount = (float) $line->debit_amount - (float) $line->credit_amount;
            if (abs($amount) < 0.00001) {
                continue;
            }

            $section = $mapSection($line->line_cash_category ?? $line->account_cash_flow_category ?? null);
            $lineName = trim((string) ($line->line_description ?? ''));
            if ($lineName === '') {
                $lineName = trim((string) ($line->entry_description ?? ''));
            }
            if ($lineName === '') {
                $lineName = (string) ($line->account_name ?? 'Unclassified');
            }

            $addLine($section, $lineName, $amount);
        }

        // totals
        $totals = [
            'operating' => $sections['operating']['total'] ?? 0.0,
            'investing' => $sections['investing']['total'] ?? 0.0,
            'financing' => $sections['financing']['total'] ?? 0.0,
        ];

        // cash at beginning: compute from journal entry lines (sum of cash account lines up to day before 'from')
        $cashBeginning = 0.0;
        try {
            $before = null;
            if ($from) {
                $dt = new \DateTime($from);
                $dt->modify('-1 day');
                $before = $dt->format('Y-m-d');
            }

            $qb = DB::table('journal_entry_lines as l')
                ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
                ->whereIn('l.account_id', $cashAccountIds);
            if ($before) $qb->where('e.entry_date', '<=', $before);
            $cashBeginning = (float) ($qb->select(DB::raw('COALESCE(SUM(l.debit_amount - l.credit_amount),0) as amt'))->value('amt') ?? 0.0);
        } catch (\Throwable $e) {
            $cashBeginning = 0.0;
        }

        // cash at end: compute from journal entry lines (sum of cash account lines up to 'to')
        $cashEnding = 0.0;
        try {
            $qb2 = DB::table('journal_entry_lines as l')
                ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
                ->whereIn('l.account_id', $cashAccountIds);
            if ($to) $qb2->where('e.entry_date', '<=', $to);
            $cashEnding = (float) ($qb2->select(DB::raw('COALESCE(SUM(l.debit_amount - l.credit_amount),0) as amt'))->value('amt') ?? 0.0);
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
