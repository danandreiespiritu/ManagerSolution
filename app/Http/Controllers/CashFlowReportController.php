<?php

namespace App\Http\Controllers;

use App\Models\CashFlowReport;
use App\Services\FinancialStatementService;
use Illuminate\Http\Request;
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
        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;
        $business = app()->bound('currentBusiness') ? app('currentBusiness') : null;
        return view('reports.financialStatements.cashflowstatement.cfsCreate', compact('business'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'method' => 'required|string|in:indirect,direct',
            'from' => 'required|date',
            'to' => 'required|date',
            'column_label' => 'nullable|string|max:255',
            'comparatives' => 'nullable|array',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = CashFlowReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'description' => $data['description'] ?? null,
            'method' => $data['method'],
            'from' => $data['from'],
            'to' => $data['to'],
            'column_label' => $data['column_label'] ?? null,
            'comparatives' => $data['comparatives'] ?? [],
        ]);

        return redirect()->route('reports.financial.cashflow.showSaved', $report->id)->with('success', 'Cash Flow report created.');
    }

    public function show(Request $request, CashFlowReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $business = app()->bound('currentBusiness') ? app('currentBusiness') : null;

        $from = $report->from?->format('Y-m-d') ?? null;
        $to = $report->to?->format('Y-m-d') ?? null;

        // main period data
        $main = $this->fs->getCashFlow((int)$businessId, $from, $to, $report->method);

        // comparatives
        $comparatives = [];
        foreach ($report->comparatives ?? [] as $col) {
            $label = $col['label'] ?? null;
            $cfrom = $col['from'] ?? null;
            $cto = $col['to'] ?? null;
            $res = $this->fs->getCashFlow((int)$businessId, $cfrom, $cto, $report->method);
            $comparatives[] = ['label' => $label, 'result' => $res];
        }

        $columnLabel = $report->column_label ?? null;
        $method = $report->method ?? 'indirect';
        $description = $report->description ?? null;
        $footer = $report->footer ?? null;
        $reportId = $report->id;

        return view('reports.financialStatements.cashflowstatement.cfsShow', compact('main','comparatives','from','to','method','description','columnLabel','footer','business','reportId'));
    }

    public function edit(CashFlowReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);
        return view('reports.financialStatements.cashflowstatement.cfsEdit', compact('report'));
    }

    public function update(Request $request, CashFlowReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $data = $request->validate([
            'description' => 'nullable|string|max:1000',
            'method' => 'required|string|in:indirect,direct',
            'from' => 'required|date',
            'to' => 'required|date',
            'column_label' => 'nullable|string|max:255',
            'comparatives' => 'nullable|array',
        ]);

        $report->update([
            'description' => $data['description'] ?? $report->description,
            'method' => $data['method'] ?? $report->method,
            'from' => $data['from'] ?? $report->from,
            'to' => $data['to'] ?? $report->to,
            'column_label' => $data['column_label'] ?? $report->column_label,
            'comparatives' => $data['comparatives'] ?? $report->comparatives,
            'footer' => $request->input('footer') ?? $report->footer,
        ]);

        return redirect()->route('reports.financial.cashflow.showSaved', $report->id)->with('success','Report updated.');
    }

    public function exportSaved(CashFlowReport $report): StreamedResponse
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $from = $report->from?->format('Y-m-d') ?? null;
        $to = $report->to?->format('Y-m-d') ?? null;

        $built = $this->fs->getCashFlow((int)$businessId, $from, $to, $report->method);

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
}
