<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfitAndLossReportRequest;
use App\Http\Requests\UpdateProfitAndLossReportRequest;
use App\Models\ProfitAndLossReport;
use App\Repositories\ProfitAndLossReport\IProfitAndLossReportRepository;
use App\Services\ProfitAndLossReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitAndLossReportController extends Controller
{
    public function __construct(
        protected IProfitAndLossReportRepository $repo,
        protected ProfitAndLossReportService $service,
    ) {
    }

    public function index(Request $request)
    {
        $reports = $this->repo->getAll($request->user()->id);

        return view('reports.financialStatements.profitandloss.plsIndex', compact('reports'));
    }

    public function create()
    {
        return view('reports.financialStatements.profitandloss.plsCreate');
    }

    public function store(StoreProfitAndLossReportRequest $request)
    {
        $data = $request->validated();

        $report = $this->repo->create($request->user()->id, [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'date_from' => $data['from'],
            'date_to' => $data['to'],
            'accounting_method' => $data['accounting_method'],
            'rounding' => $data['rounding'] ?? 'off',
            'footer' => $data['footer'] ?? null,
        ]);

        return redirect()->route('reports.financial.profit-and-loss.show', $report->id)
            ->with('success', 'Profit and Loss report created.');
    }

    public function show(ProfitAndLossReport $report)
    {
        // enforce ownership (business scope is already applied via global scope)
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $businessName = app()->bound('currentBusiness') && ($b = app('currentBusiness'))
            ? ($b->business_name ?? config('app.name'))
            : config('app.name');

        $built = $this->service->build(
            (int) $report->business_id,
            $report->date_from->format('Y-m-d'),
            $report->date_to->format('Y-m-d'),
            (string) $report->accounting_method,
            (string) $report->rounding
        );

        $grouped = $built['grouped'];
        $netProfit = $built['netProfit'];

        // Normalize account keys for view compatibility (use 'name' and 'code')
        foreach ($grouped as $gName => $gData) {
            foreach ($gData['accounts'] as $i => $acc) {
                $grouped[$gName]['accounts'][$i]['name'] = $acc['account_name'] ?? ($acc['name'] ?? '');
                $grouped[$gName]['accounts'][$i]['code'] = $acc['account_code'] ?? ($acc['code'] ?? null);
            }
        }

        // Split grouped accounts into revenue vs expense for the view
        $revenueGroups = [];
        $expenseGroups = [];
        foreach ($grouped as $gName => $gData) {
            $low = strtolower((string) $gName);
            if (str_contains($low, 'revenue') || str_contains($low, 'income') || str_contains($low, 'sales') || str_contains($low, 'gain')) {
                $revenueGroups[$gName] = $gData;
            } else {
                $expenseGroups[$gName] = $gData;
            }
        }

        $revenueTotal = $built['totalRevenue'] ?? 0.0;
        $expenseTotal = $built['totalExpense'] ?? 0.0;
        $isProfit = ((float) $netProfit) >= 0;

        // Formatter used by several report views
        $fmt = function ($v, $showSign = false) {
            if ($v === null) return '—';
            $v = (float) $v;
            if (abs($v) < 0.005) return '—';
            if ($v < 0) {
                return '(' . number_format(abs($v), 2) . ')';
            }
            $formatted = number_format($v, 2);
            return $showSign && $v > 0 ? '+' . $formatted : $formatted;
        };

        // dd([
        //     'revenue_groups_detected' => array_keys($revenueGroups),
        //     'revenue_groups_full' => $revenueGroups,
        //     'service_totalRevenue' => $revenueTotal,
        //     'service_totalExpense' => $expenseTotal,
        //     'netProfit' => $netProfit,
        // ]);

        return view('reports.financialStatements.profitandloss.plsShow', compact(
            'report',
            'grouped',
            'netProfit',
            'businessName',
            'revenueGroups',
            'expenseGroups',
            'revenueTotal',
            'expenseTotal',
            'isProfit',
            'fmt'
        ));
    }

    public function edit(ProfitAndLossReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        return view('reports.financialStatements.profitandloss.plsEdit', compact('report'));
    }

    public function update(UpdateProfitAndLossReportRequest $request, ProfitAndLossReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $data = $request->validated();

        $updated = $this->repo->update($request->user()->id, (int) $report->id, [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'date_from' => $data['from'],
            'date_to' => $data['to'],
            'accounting_method' => $data['accounting_method'],
            'rounding' => $data['rounding'] ?? 'off',
            'footer' => $data['footer'] ?? null,
        ]);

        if (! $updated) {
            return redirect()->back()->with('error', 'Unable to update report.');
        }

        return redirect()->route('reports.financial.profit-and-loss.show', $report->id)
            ->with('success', 'Report updated.');
    }

    public function export(ProfitAndLossReport $report): StreamedResponse
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $built = $this->service->build(
            (int) $report->business_id,
            $report->date_from->format('Y-m-d'),
            $report->date_to->format('Y-m-d'),
            (string) $report->accounting_method,
            (string) $report->rounding
        );

        $filename = 'profit-and-loss-report-' . $report->id . '.csv';

        return response()->streamDownload(function () use ($built) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Group', 'Account Code', 'Account Name', 'Amount']);

            foreach ($built['grouped'] as $groupName => $data) {
                foreach ($data['accounts'] as $acc) {
                    fputcsv($out, [
                        $groupName,
                        $acc['account_code'],
                        $acc['account_name'],
                        number_format((float) $acc['amount'], 2, '.', ''),
                    ]);
                }
                // Group total row
                fputcsv($out, [$groupName . ' Total', '', '', number_format((float) $data['total'], 2, '.', '')]);
            }

            fputcsv($out, ['Net Profit/Loss', '', '', number_format((float) $built['netProfit'], 2, '.', '')]);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
