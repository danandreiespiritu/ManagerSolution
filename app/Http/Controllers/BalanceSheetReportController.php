<?php

namespace App\Http\Controllers;

use App\Models\BalanceSheetReport;
use App\Models\ChartofAccounts;
use App\Services\FinancialStatementService;
use Illuminate\Http\Request;

class BalanceSheetReportController extends Controller
{
    public function __construct(protected FinancialStatementService $fs)
    {
    }

    public function index(Request $request)
    {
        $reports = BalanceSheetReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return view('reports.financialStatements.balancesheet.blsIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;
        $accounts = ChartofAccounts::where('business_id', $businessId)
            ->where('account_type', 'BL')
            ->orderBy('account_name')
            ->get();

        return view('reports.financialStatements.balancesheet.blsCreate', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'accounting_method' => 'required|string',
            'layout' => 'nullable|string',
            'columns' => 'nullable|array',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = BalanceSheetReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'title' => $data['title'],
            'date' => $data['date'],
            'accounting_method' => $data['accounting_method'],
            'layout' => $data['layout'] ?? 'assets-minus-liabilities-equals-equity',
            'columns' => $data['columns'] ?? [],
            'footer' => $request->input('footer') ?? null,
        ]);

        return redirect()->route('reports.financial.balance-sheet.show', $report->id)->with('success', 'Report created.');
    }

    public function show(BalanceSheetReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $business = app()->bound('currentBusiness') ? app('currentBusiness') : null;

        // main period
        $main = $this->fs->getBalanceSheet((int)$businessId, $report->date?->format('Y-m-d') ?? null);

        // comparatives: for each column, compute totals per section and per-account balances
        $sectionComparativeTotals = [];
        $comparativeColMaps = []; // colId => [account_id => balance]
        foreach ($report->columns ?? [] as $col) {
            $colId = $col->id ?? ($col['id'] ?? null) ?? uniqid();
            $colDate = $col->date ?? ($col['date'] ?? null) ?? null;
            $comp = $this->fs->getBalanceSheet((int)$businessId, $colDate);

            // build per-account map for this comparative column
            $map = [];
            foreach (['assets','liabilities','equity'] as $sec) {
                foreach ($comp[$sec] ?? [] as $row) {
                    $acctId = $row->account_id ?? $row->accountId ?? null;
                    if ($acctId === null) continue;
                    $map[$acctId] = (float) ($row->balance ?? 0);
                }
            }

            $comparativeColMaps[$colId] = $map;
            $sectionComparativeTotals[$colId] = [
                'assets' => $comp['total_assets'] ?? 0.0,
                'liabilities' => $comp['total_liabilities'] ?? 0.0,
                'equity' => $comp['total_equity'] ?? 0.0,
            ];
        }

        // build sections grouped by 'group' (e.g., Current Assets / Non-Current Assets)
        $sections = [];
        foreach (['assets','liabilities','equity'] as $secKey) {
            $rows = $main[$secKey] ?? [];
            $groups = [];
            foreach ($rows as $r) {
                // attach comparatives map to each row for view consumption
                $r->comparatives = [];
                foreach ($report->columns ?? [] as $col) {
                    $colId = $col->id ?? ($col['id'] ?? null) ?? uniqid();
                    $r->comparatives[$colId] = $comparativeColMaps[$colId][$r->account_id ?? null] ?? 0.0;
                }

                $grp = $r->group ?? ($r->group_category ?? 'Other');
                if (! isset($groups[$grp])) $groups[$grp] = [];
                $groups[$grp][] = $r;
            }

            // compute totals per section
            $total = $main["total_{$secKey}"] ?? 0.0;

            $sections[$secKey] = ['title' => ucfirst($secKey), 'groups' => $groups, 'total' => $total];
        }

        $layout = $report->layout ?? 'assets-minus-liabilities-equals-equity';

        // compute net assets for layout that shows net assets
        $netAssets = ($sections['assets']['total'] ?? 0.0) - ($sections['liabilities']['total'] ?? 0.0);
        $netAssetsComparatives = [];
        foreach ($sectionComparativeTotals as $colId => $vals) {
            $netAssetsComparatives[$colId] = ($vals['assets'] ?? 0.0) - ($vals['liabilities'] ?? 0.0);
        }

        $liabilitiesTotal = $sections['liabilities']['total'] ?? 0.0;
        $equityTotal = $sections['equity']['total'] ?? 0.0;

        return view('reports.financialStatements.balancesheet.blsShow', compact('report','business','sections','sectionComparativeTotals','layout','netAssets','netAssetsComparatives','liabilitiesTotal','equityTotal'));
    }

    public function edit(BalanceSheetReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);
        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $accounts = ChartofAccounts::where('business_id', $businessId)->where('account_type','BL')->orderBy('account_name')->get();
        return view('reports.financialStatements.balancesheet.blsEdit', compact('report','accounts'));
    }

    public function update(Request $request, BalanceSheetReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'accounting_method' => 'required|string',
            'layout' => 'nullable|string',
            'columns' => 'nullable|array',
        ]);

        $report->update([
            'title' => $data['title'],
            'date' => $data['date'],
            'accounting_method' => $data['accounting_method'],
            'layout' => $data['layout'] ?? $report->layout,
            'columns' => $data['columns'] ?? $report->columns,
            'footer' => $request->input('footer') ?? $report->footer,
        ]);

        return redirect()->route('reports.financial.balance-sheet.show', $report->id)->with('success','Report updated.');
    }
}
