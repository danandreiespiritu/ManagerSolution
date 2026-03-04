<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralLedgerSummaryReport;
use App\Repositories\GeneralLedgerSummaryReport\IGeneralLedgerSummaryReportRepository;
use App\Services\GeneralLedgerService;

class GeneralLedgerSummaryReportController extends Controller
{
    public function __construct(
        protected IGeneralLedgerSummaryReportRepository $repo,
        protected GeneralLedgerService $ledger
    ) {
    }

    public function index(Request $request)
    {
        $reports = $this->repo->getAll($request->user()->id);
        return view('reports.generalLedger.generalLedger.generalLedgerIndex', compact('reports'));
    }

    public function create()
    {
        return view('reports.generalLedger.generalLedger.generalLedgerCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'show_codes' => 'sometimes|boolean',
            'exclude_zero' => 'sometimes|boolean',
        ]);

        $report = $this->repo->create($request->user()->id, [
            'business_id' => $request->user()->current_business_id ?? null,
            'description' => $data['description'] ?? null,
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'show_codes' => ! empty($data['show_codes']),
            'exclude_zero' => ! empty($data['exclude_zero']),
        ]);

        return redirect()->route('reports.general-ledger.summary.show', $report->id)->with('success', 'General ledger summary created.');
    }

    public function show(GeneralLedgerSummaryReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);

        $from = $report->from_date?->format('Y-m-d') ?? null;
        $to = $report->to_date?->format('Y-m-d') ?? null;

        $accounts = $this->ledger->getGeneralLedgerSummary((int) $businessId, $from, $to, (bool) $report->exclude_zero);

        return view('reports.generalLedger.generalLedger.generalLedgerShow', compact('report','accounts'));
    }
}
