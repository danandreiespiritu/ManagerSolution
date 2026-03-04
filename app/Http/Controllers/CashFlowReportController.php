<?php

namespace App\Http\Controllers;

use App\Models\CashFlowReport;
use App\Services\FinancialStatementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CashFlowReportController extends Controller
{
    public function __construct(protected FinancialStatementService $fs)
    {
    }

    public function index(Request $request)
    {
        $reports = CashFlowReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return view('reports.financialStatements.cashflowstatement.cfsIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        $business = app()->bound('currentBusiness') ? app('currentBusiness') : null;

        $businessId = $business?->id ?? null;
        // fetch accounts that have a cash_flow_category or look like cash/bank accounts
        $cashFlowAccounts = DB::table('chart_of_accounts')
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNotNull('cash_flow_category')->where('cash_flow_category', '!=', '')
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%bank%'");
            })
            ->orderBy('cash_flow_category')
            ->orderBy('account_name')
            ->get();

        return view('reports.financialStatements.cashflowstatement.cfsCreate', compact('business','cashFlowAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'method' => 'required|string|in:indirect,direct',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'cash_flow_accounts' => 'nullable|array',
            'column_label' => 'nullable|string|max:255',
            'comparatives' => 'nullable|array',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;
        $cashTransactions = $this->fetchCashTransactions((int) $businessId, $data['from'], $data['to']);

        // build selected account overrides for accurate computation
        $selectedAccountOverrides = collect($request->input('cash_flow_accounts', []))
            ->filter(fn($v) => data_get($v, 'selected'))
            ->map(fn($v, $k) => [ 'account_id' => (int) (data_get($v, 'account_id') ?? $k) ])
            ->values()
            ->all();

        // validate the core balancing rule before persisting:
        // Net Increase in Cash == Operating + Investing + Financing
        $built = $this->fs->getCashFlow((int)$businessId, $data['from'], $data['to'], $data['method'], $selectedAccountOverrides);
        $operating = (float) data_get($built, 'totals.operating', 0);
        $investing = (float) data_get($built, 'totals.investing', 0);
        $financing = (float) data_get($built, 'totals.financing', 0);
        $netIncrease = (float) data_get($built, 'netIncrease', 0);
        $tolerance = 0.01;
        if (abs(($operating + $investing + $financing) - $netIncrease) > $tolerance) {
            return back()->withInput()->withErrors(['balance' => 'Cash Flow Statement is NOT Balanced. Please review entries.']);
        }

        $payload = [
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'description' => $data['description'] ?? null,
            'method' => $data['method'],
            'from' => $data['from'],
            'to' => $data['to'],
            'cash_flow_accounts' => $data['cash_flow_accounts'] ?? $request->input('cash_flow_accounts', []),
            'column_label' => $data['column_label'] ?? null,
            'comparatives' => $data['comparatives'] ?? [],
        ];

        if (Schema::hasColumn('cash_flow_reports', 'cash_transactions')) {
            $payload['cash_transactions'] = $cashTransactions;
        }

        $report = CashFlowReport::create($payload);

        return redirect()->route('reports.financial.cashflow.showSaved', $report->id)->with('success', 'Cash Flow report created.');
    }

    public function show(Request $request, CashFlowReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $business = app()->bound('currentBusiness') ? app('currentBusiness') : null;

        $from = $report->from?->format('Y-m-d') ?? null;
        $to = $report->to?->format('Y-m-d') ?? null;

        $selectedAccountOverrides = collect($report->cash_flow_accounts ?? [])
            ->filter(fn($v) => data_get($v, 'selected'))
            ->map(fn($v, $k) => [
                'account_id' => (int) (data_get($v, 'account_id') ?? $k),
            ])
            ->values()
            ->all();

        // main period data
        $main = $this->fs->getCashFlow((int)$businessId, $from, $to, $report->method, $selectedAccountOverrides);

        // comparatives
        $comparatives = [];
        foreach ($report->comparatives ?? [] as $col) {
            $label = $col['label'] ?? null;
            $cfrom = $col['from'] ?? null;
            $cto = $col['to'] ?? null;
            $res = $this->fs->getCashFlow((int)$businessId, $cfrom, $cto, $report->method, $selectedAccountOverrides);
            $comparatives[] = ['label' => $label, 'result' => $res];
        }

        $columnLabel = $report->column_label ?? null;
        $method = $report->method ?? 'indirect';
        $description = $report->description ?? null;
        $footer = $report->footer ?? null;
        $reportId = $report->id;

        $cashTransactions = collect();
        if (Schema::hasColumn('cash_flow_reports', 'cash_transactions')) {
            $cashTransactions = collect($report->cash_transactions ?? []);
        }
        if ($cashTransactions->isEmpty()) {
            $cashTransactions = collect($this->fetchCashTransactions((int) $businessId, $from, $to));
        }

        // If the report has saved cash_flow_accounts, filter to only selected accounts
        $selectedAccountIds = collect($report->cash_flow_accounts ?? [])
            ->filter(fn($v) => data_get($v, 'selected'))
            ->map(fn($v, $k) => (int) (data_get($v, 'account_id') ?? $k))
            ->values()
            ->all();

        if (! empty($selectedAccountIds)) {
            $cashTransactions = $cashTransactions->filter(fn($r) => in_array((int) ($r['account_id'] ?? 0), $selectedAccountIds, true))->values();
        }

        // Group transactions by cash_flow_category for separate display
        $txGroups = $cashTransactions->groupBy(fn($r) => trim((string) ($r['cash_flow_category'] ?? 'Unclassified')))->toArray();

        return view('reports.financialStatements.cashflowstatement.cfsShow', compact('main','comparatives','from','to','method','description','columnLabel','footer','business','reportId','cashTransactions','txGroups'));
    }

    public function edit(CashFlowReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $cashFlowAccounts = DB::table('chart_of_accounts')
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNotNull('cash_flow_category')->where('cash_flow_category', '!=', '')
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%bank%'");
            })
            ->orderBy('cash_flow_category')
            ->orderBy('account_name')
            ->get();

        return view('reports.financialStatements.cashflowstatement.cfsEdit', compact('report','cashFlowAccounts'));
    }

    public function update(Request $request, CashFlowReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'method' => 'required|string|in:indirect,direct',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'column_label' => 'nullable|string|max:255',
            'comparatives' => 'nullable|array',
        ]);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $cashTransactions = $this->fetchCashTransactions((int) $businessId, $data['from'], $data['to']);

        // build selected account overrides from incoming payload or existing report
        $inputAccounts = $request->input('cash_flow_accounts', $report->cash_flow_accounts ?? []);
        $selectedAccountOverrides = collect($inputAccounts)
            ->filter(fn($v) => data_get($v, 'selected'))
            ->map(fn($v, $k) => [ 'account_id' => (int) (data_get($v, 'account_id') ?? $k) ])
            ->values()
            ->all();

        // validate balancing rule before updating:
        // Net Increase in Cash == Operating + Investing + Financing
        $built = $this->fs->getCashFlow((int)$businessId, $data['from'], $data['to'], $data['method'], $selectedAccountOverrides);
        $operating = (float) data_get($built, 'totals.operating', 0);
        $investing = (float) data_get($built, 'totals.investing', 0);
        $financing = (float) data_get($built, 'totals.financing', 0);
        $netIncrease = (float) data_get($built, 'netIncrease', 0);
        $tolerance = 0.01;
        if (abs(($operating + $investing + $financing) - $netIncrease) > $tolerance) {
            return back()->withInput()->withErrors(['balance' => 'Cash Flow Statement is NOT Balanced. Please review entries.']);
        }

        $payload = [
            'description' => $data['description'] ?? $report->description,
            'method' => $data['method'] ?? $report->method,
            'from' => $data['from'] ?? $report->from,
            'to' => $data['to'] ?? $report->to,
            'column_label' => $data['column_label'] ?? $report->column_label,
            'comparatives' => $data['comparatives'] ?? $report->comparatives,
            'cash_flow_accounts' => $request->input('cash_flow_accounts', $report->cash_flow_accounts ?? []),
            'footer' => $request->input('footer') ?? $report->footer,
        ];

        if (Schema::hasColumn('cash_flow_reports', 'cash_transactions')) {
            $payload['cash_transactions'] = $cashTransactions;
        }

        $report->update($payload);

        return redirect()->route('reports.financial.cashflow.showSaved', $report->id)->with('success','Report updated.');
    }

    public function exportSaved(CashFlowReport $report): StreamedResponse
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $from = $report->from?->format('Y-m-d') ?? null;
        $to = $report->to?->format('Y-m-d') ?? null;

        $selectedAccountOverrides = collect($report->cash_flow_accounts ?? [])
            ->filter(fn($v) => data_get($v, 'selected'))
            ->map(fn($v, $k) => [
                'account_id' => (int) (data_get($v, 'account_id') ?? $k),
            ])
            ->values()
            ->all();

        $built = $this->fs->getCashFlow((int)$businessId, $from, $to, $report->method, $selectedAccountOverrides);

        $filename = 'cashflow-' . $report->id . '.csv';

        return response()->streamDownload(function () use ($built) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Section','Line','Amount']);
            foreach (['operating','investing','financing'] as $sec) {
                foreach ($built['sections'][$sec]['lines'] ?? [] as $line) {
                    fputcsv($out, [$sec, $line['name'], number_format((float)$line['amount'],2,'.','')]);
                }
                fputcsv($out, [$sec . ' Total', '', number_format((float)$built['sections'][$sec]['total'],2,'.','')]);
            }
            fputcsv($out, ['Net Increase', '', number_format((float)$built['netIncrease'],2,'.','')]);
            fputcsv($out, ['Cash Beginning', '', number_format((float)$built['cashBeginning'],2,'.','')]);
            fputcsv($out, ['Cash Ending', '', number_format((float)$built['cashEnding'],2,'.','')]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function export(Request $request): StreamedResponse
    {
        // build on-the-fly from query params: from,to,method
        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;
        $from = $request->query('from');
        $to = $request->query('to');
        $method = $request->query('method','indirect');

        $built = $this->fs->getCashFlow((int)$businessId, $from, $to, $method);

        $filename = 'cashflow-export-' . now()->format('YmdHis') . '.csv';

        return response()->streamDownload(function () use ($built) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Section','Line','Amount']);
            foreach (['operating','investing','financing'] as $sec) {
                foreach ($built['sections'][$sec]['lines'] ?? [] as $line) {
                    fputcsv($out, [$sec, $line['name'], number_format((float)$line['amount'],2,'.','')]);
                }
                fputcsv($out, [$sec . ' Total', '', number_format((float)$built['sections'][$sec]['total'],2,'.','')]);
            }
            fputcsv($out, ['Net Increase', '', number_format((float)$built['netIncrease'],2,'.','')]);
            fputcsv($out, ['Cash Beginning', '', number_format((float)$built['cashBeginning'],2,'.','')]);
            fputcsv($out, ['Cash Ending', '', number_format((float)$built['cashEnding'],2,'.','')]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function fetchCashTransactions(?int $businessId = null, ?string $from = null, ?string $to = null): array
    {
        $rows = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('chart_of_accounts as a', 'a.id', '=', 'l.account_id')
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->where(function ($q) {
                $q->whereRaw("LOWER(COALESCE(a.cash_flow_category,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%bank%'");
            })
            ->when($from, fn($q) => $q->where('e.entry_date', '>=', $from))
            ->when($to, fn($q) => $q->where('e.entry_date', '<=', $to))
            ->orderBy('e.entry_date')
            ->orderBy('l.id')
            ->select(
                'e.id as entry_id',
                'l.account_id as account_id',
                'e.entry_date',
                'e.reference_type',
                'e.reference_id',
                'a.account_name',
                'a.account_code',
                'a.cash_flow_category',
                'l.description',
                'l.debit_amount',
                'l.credit_amount'
            )
            ->get();

        return $rows->map(function ($r) {
            return [
                'entry_id' => $r->entry_id,
                'account_id' => $r->account_id ?? null,
                'entry_date' => $r->entry_date,
                'reference' => trim(((string) ($r->reference_type ?? '')) . ' ' . ((string) ($r->reference_id ?? ''))),
                'account_name' => $r->account_name,
                'account_code' => $r->account_code,
                'cash_flow_category' => $r->cash_flow_category ?? null,
                'description' => $r->description,
                'debit' => (float) $r->debit_amount,
                'credit' => (float) $r->credit_amount,
                'amount' => (float) $r->debit_amount - (float) $r->credit_amount,
            ];
        })->values()->all();
    }
}
