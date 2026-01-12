<?php

namespace App\Http\Controllers;

use App\Models\ProfitAndLossActualVsBudgetReport;
use App\Models\ChartofAccounts;
use App\Services\FinancialStatementService;
use Illuminate\Http\Request;

class ProfitAndLossActualVsBudgetController extends Controller
{
    public function __construct(protected FinancialStatementService $fs)
    {
    }

    public function index(Request $request)
    {
        $reports = ProfitAndLossActualVsBudgetReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return view('reports.financialStatements.actualvsbudget.plsActualvsBudgetIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;
        $accounts = ChartofAccounts::where('business_id', $businessId)
            ->where('account_type', 'PL')
            ->orderBy('account_name')
            ->get();

        return view('reports.financialStatements.actualvsbudget.plsActualvsBudgetCreate', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'from' => 'required|date',
            'to' => 'required|date',
            'accounting_method' => 'required|string',
            'lines' => 'nullable|array',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = ProfitAndLossActualVsBudgetReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'title' => $data['title'],
            'date_from' => $data['from'],
            'date_to' => $data['to'],
            'accounting_method' => $data['accounting_method'],
            'lines' => $data['lines'] ?? [],
            'footer' => $request->input('footer') ?? null,
        ]);

        return redirect()->route('reports.financial.profit-and-loss.actual-and-budget.show', $report->id)
            ->with('success', 'Report created.');
    }

    public function show(ProfitAndLossActualVsBudgetReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $businessName = app()->bound('currentBusiness') && ($b = app('currentBusiness'))
            ? ($b->business_name ?? config('app.name'))
            : config('app.name');

        $built = $this->fs->getProfitAndLoss((int)$report->business_id, $report->date_from->format('Y-m-d'), $report->date_to->format('Y-m-d'));

        // Map balances by account id
        $balances = [];
        foreach (['income','expense'] as $k) {
            foreach ($built[$k] ?? [] as $row) {
                $balances[$row->account_id] = $row->balance;
            }
        }

        $lines = $report->lines ?? [];
        $groups = [];
        foreach ($lines as $ln) {
            $accId = $ln['account_id'] ?? null;
            if (! $accId) continue;
            $acct = ChartofAccounts::find($accId);
            $name = $acct->account_name ?? ($acct->name ?? ('Account #' . $accId));
            $actual = (float) ($balances[$accId] ?? 0.0);
            $budget = (float) ($ln['amount'] ?? 0.0);
            $remaining = $budget - $actual;
            $percentage = $budget != 0.0 ? ($actual / $budget) * 100.0 : null;

            // classify group
            $lname = strtolower($acct->account_name ?? '');
            $groupName = (str_contains($lname,'sale') || str_contains($lname,'revenue') || str_contains($lname,'income')) ? 'Revenue' : 'Expenses';

            if (! isset($groups[$groupName])) {
                $groups[$groupName] = ['accounts' => [], 'totals' => ['actual' => 0.0, 'budget' => 0.0, 'remaining' => 0.0, 'percentage' => null]];
            }

            $groups[$groupName]['accounts'][] = [
                'name' => $name,
                'actual' => $actual,
                'budget' => $budget,
                'percentage' => $percentage,
                'remaining' => $remaining,
            ];

            $groups[$groupName]['totals']['actual'] += $actual;
            $groups[$groupName]['totals']['budget'] += $budget;
            $groups[$groupName]['totals']['remaining'] += $remaining;
        }

        // compute percentage totals
        foreach ($groups as $gn => $gd) {
            $b = $gd['totals']['budget'] ?? 0.0;
            $a = $gd['totals']['actual'] ?? 0.0;
            $groups[$gn]['totals']['percentage'] = $b != 0.0 ? ($a / $b) * 100.0 : null;
        }

        return view('reports.financialStatements.actualvsbudget.show', ['report' => $report, 'businessName' => $businessName, 'grouped' => $groups]);
    }

    public function edit(ProfitAndLossActualVsBudgetReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) abort(403);
        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;
        $accounts = ChartofAccounts::where('business_id', $businessId)->where('account_type','PL')->orderBy('account_name')->get();
        return view('reports.financialStatements.actualvsbudget.edit', compact('report','accounts'));
    }

    public function update(Request $request, ProfitAndLossActualVsBudgetReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) abort(403);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'from' => 'required|date',
            'to' => 'required|date',
            'accounting_method' => 'required|string',
            'lines' => 'nullable|array',
        ]);

        $report->update([
            'title' => $data['title'],
            'date_from' => $data['from'],
            'date_to' => $data['to'],
            'accounting_method' => $data['accounting_method'],
            'lines' => $data['lines'] ?? [],
            'footer' => $request->input('footer') ?? null,
        ]);

        return redirect()->route('reports.financial.profit-and-loss.actual-and-budget.show', $report->id)->with('success','Report updated.');
    }
}
