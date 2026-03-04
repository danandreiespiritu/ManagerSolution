<?php

namespace App\Http\Controllers;

use App\Models\ChangesInEquityReport;
use App\Services\FinancialStatementService;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;

class ChangesInEquityReportController extends Controller
{
    public function __construct(protected FinancialStatementService $fs, protected GeneralLedgerService $ledger)
    {
    }

    public function index(Request $request)
    {
        $reports = ChangesInEquityReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reports.financialStatements.sce.sceIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.financialStatements.sce.sceCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'accounting_method' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date',
            'column_label' => 'nullable|string|max:255',
            'comparatives' => 'nullable|array',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = ChangesInEquityReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'description' => $data['description'] ?? null,
            'accounting_method' => $data['accounting_method'],
            'from' => $data['from'],
            'to' => $data['to'],
            'column_label' => $data['column_label'] ?? null,
            'comparatives' => $data['comparatives'] ?? null,
            'footer' => $request->input('footer') ?? null,
        ]);

        return redirect()->route('reports.financial.changes-in-equity.show', $report->id)->with('success', 'Report created.');
    }

    public function show(ChangesInEquityReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $business = app()->bound('currentBusiness') ? app('currentBusiness') : null;

        $from = $report->from?->format('Y-m-d') ?? null;
        $to = $report->to?->format('Y-m-d') ?? null;

        // Get totals from Profit & Loss service
        $plBuilt = app(\App\Services\ProfitAndLossReportService::class)->build(
            (int) $businessId,
            $from,
            $to,
            (string) ($report->accounting_method ?? 'accrual'),
            (string) ($report->rounding ?? 'none')
        );

        $revenueTotal = (float) ($plBuilt['totalRevenue'] ?? 0.0);
        $expenseTotal = (float) ($plBuilt['totalExpense'] ?? 0.0);

        // Net Income = Revenue - Expenses
        $netIncome = $revenueTotal - $expenseTotal;

        // beginning equity: as of day before from
        $before = null;
        try {
            if ($from) {
                $dt = new \DateTime($from);
                $dt->modify('-1 day');
                $before = $dt->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            $before = null;
        }

        $bsBefore = $this->fs->getBalanceSheet((int)$businessId, $before);
        $bsEnd = $this->fs->getBalanceSheet((int)$businessId, $to);

        $beginningEquity = (float) ($bsBefore['total_equity'] ?? 0.0);
        $endingEquity = (float) ($bsEnd['total_equity'] ?? 0.0);

        // attempt to detect owner investments and withdrawals by account name heuristics
        $periodBalances = $this->ledger->getAccountBalances((int)$businessId, $from, $to);

        $investmentKeywords = ['invest', 'capital', 'share', 'contribut', 'paid in'];
        $withdrawKeywords = ['dividend', 'withdraw', 'drawing', 'distribution'];

        $investments = 0.0;
        $withdrawals = 0.0;

        

        foreach ($periodBalances as $b) {
            $name = strtolower((string)($b->account_name ?? ''));
            // investments: net credits (credit > debit) for matching accounts => balance < 0 in ledger (debit - credit)
            foreach ($investmentKeywords as $kw) {
                if (str_contains($name, $kw)) {
                    $investments += max(0, -1 * (float)$b->balance);
                    continue 2;
                }
            }

            // withdrawals/dividends: net debits (debit > credit) for matching accounts => balance > 0
            foreach ($withdrawKeywords as $kw) {
                if (str_contains($name, $kw)) {
                    $withdrawals += max(0, (float)$b->balance);
                    continue 2;
                }
            }
        }

        // compute a verified ending equity from the core formula
        $verifiedEndingEquity = $beginningEquity + $netIncome + $investments - $withdrawals;

        // Build opening and ending per-account maps for equity accounts (used by the view)
        $openingMap = [];
        foreach ($bsBefore['equity'] ?? [] as $row) {
            $acctId = $row->account_id ?? null;
            if ($acctId === null) continue;
            $openingMap[$acctId] = (float) ($row->balance ?? 0.0);
        }

        $endingMap = [];
        foreach ($bsEnd['equity'] ?? [] as $row) {
            $acctId = $row->account_id ?? null;
            if ($acctId === null) continue;
            $endingMap[$acctId] = (float) ($row->balance ?? 0.0);
        }

        // Prepare sections for accrual layout: group equity accounts by group label
        $sections = [];
        foreach ($bsEnd['equity'] ?? [] as $r) {
            $grp = $r->group ?? ($r->group_category ?? 'Equity');
            if (! isset($sections[$grp])) $sections[$grp] = ['accounts' => [], 'opening' => 0.0, 'movement' => 0.0, 'ending' => 0.0];
            $sections[$grp]['accounts'][] = [
                'id' => $r->account_id ?? null,
                'name' => $r->account_name ?? null,
            ];
        }

        // compute group totals
        foreach ($sections as $grp => &$sec) {
            $open = 0.0; $end = 0.0; $movement = 0.0;
            foreach ($sec['accounts'] as $a) {
                $id = $a['id'];
                $o = $openingMap[$id] ?? 0.0;
                $e = $endingMap[$id] ?? 0.0;
                $open += $o;
                $end += $e;
                $movement += ($e - $o);
            }
            $sec['opening'] = $open;
            $sec['movement'] = $movement;
            $sec['ending'] = $end;
        }
        unset($sec);

        $profitLoss = $netIncome;

        $reportTitle = 'Statement of Changes in Equity';

        // accounting basis for view (cash/accrual)
        $basis = $report->accounting_method ?? 'accrual';

        return view('reports.financialStatements.sce.sceShow', compact(
            'report','business','from','to','beginningEquity','endingEquity','netIncome','investments','withdrawals','verifiedEndingEquity','openingMap','endingMap','sections','profitLoss','basis'
        ));
    }

    public function edit(ChangesInEquityReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);
        return view('reports.financialStatements.sce.sceCreate', compact('report'));
    }

    public function update(Request $request, ChangesInEquityReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);
        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'accounting_method' => 'required|string',
            'from' => 'required|date',
            'to' => 'required|date',
            'column_label' => 'nullable|string|max:255',
            'comparatives' => 'nullable|array',
        ]);

        $report->update([
            'description' => $data['description'] ?? $report->description,
            'accounting_method' => $data['accounting_method'],
            'from' => $data['from'],
            'to' => $data['to'],
            'column_label' => $data['column_label'] ?? $report->column_label,
            'comparatives' => $data['comparatives'] ?? $report->comparatives,
            'footer' => $request->input('footer') ?? $report->footer,
        ]);

        return redirect()->route('reports.financial.changes-in-equity.show', $report->id)->with('success','Report updated.');
    }
}
