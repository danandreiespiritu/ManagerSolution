<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfitAndLossReportService
{
    public function build(int $businessId, string $from, string $to, string $accountingMethod = 'accrual', string $rounding = 'off'): array
    {
        // NOTE: True cash-basis reporting requires different recognition rules.
        // This implementation derives amounts from posted journal entries within the period.
        // The accounting_method is stored and surfaced for UI, but does not currently change recognition.

        $rows = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('chart_of_accounts as a', 'a.id', '=', 'l.account_id')
            ->where('l.business_id', $businessId)
            ->where('a.account_type', 'PL')
            ->whereDate('e.entry_date', '>=', $from)
            ->whereDate('e.entry_date', '<=', $to)
            ->groupBy('l.account_id', 'a.account_code', 'a.account_name', 'a.account_group')
            ->select([
                'l.account_id',
                'a.account_code',
                'a.account_name',
                'a.account_group',
                DB::raw('COALESCE(SUM(l.debit_amount),0) as total_debit'),
                DB::raw('COALESCE(SUM(l.credit_amount),0) as total_credit'),
                DB::raw('COALESCE(SUM(l.debit_amount - l.credit_amount),0) as raw_balance'),
            ])
            ->get();

        $grouped = [];
        $totalRevenue = 0.0;
        $totalExpense = 0.0;

        foreach ($rows as $r) {
            $groupName = trim((string) ($r->account_group ?? ''));
            if ($groupName === '') {
                $groupName = 'Other';
            }

            // Normalize amounts for display: always positive.
            // For P&L presentation we treat both revenue and expense as positive figures.
            $amount = (float) abs((float) $r->raw_balance);
            $amount = $this->applyRounding($amount, $rounding);

            if (! isset($grouped[$groupName])) {
                $grouped[$groupName] = [
                    'accounts' => [],
                    'total' => 0.0,
                ];
            }

            $grouped[$groupName]['accounts'][] = [
                'account_id' => (int) $r->account_id,
                'account_code' => $r->account_code,
                'account_name' => $r->account_name,
                'amount' => $amount,
                'total_debit' => (float) $r->total_debit,
                'total_credit' => (float) $r->total_credit,
                'raw_balance' => (float) $r->raw_balance,
            ];

            $grouped[$groupName]['total'] = $this->applyRounding(((float) $grouped[$groupName]['total']) + $amount, $rounding);

            if ($this->isRevenueGroup($groupName)) {
                $totalRevenue = $this->applyRounding($totalRevenue + $amount, $rounding);
            } else {
                $totalExpense = $this->applyRounding($totalExpense + $amount, $rounding);
            }
        }

        // Sort groups by name; sort accounts by amount desc
        ksort($grouped, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($grouped as $g => $data) {
            usort($grouped[$g]['accounts'], fn ($a, $b) => ($b['amount'] <=> $a['amount']));
        }

        $netProfit = $this->applyRounding($totalRevenue - $totalExpense, $rounding);

        return [
            'grouped' => $grouped,
            'netProfit' => $netProfit,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'meta' => [
                'business_id' => $businessId,
                'from' => $from,
                'to' => $to,
                'accounting_method' => $accountingMethod,
                'rounding' => $rounding,
            ],
        ];
    }

    private function applyRounding(float $value, string $rounding): float
    {
        $rounding = (string) $rounding;

        if ($rounding === 'off' || $rounding === '') {
            return round($value, 2);
        }

        if ($rounding === 'nearest' || $rounding === '1') {
            return (float) round($value, 0);
        }

        if (is_numeric($rounding)) {
            $step = (float) $rounding;
            if ($step <= 0) {
                return round($value, 2);
            }
            return (float) (round($value / $step, 0) * $step);
        }

        return round($value, 2);
    }

    private function isRevenueGroup(string $groupName): bool
    {
        $name = Str::lower($groupName);
        return Str::contains($name, ['revenue', 'income', 'sales', 'gain']);
    }
}
