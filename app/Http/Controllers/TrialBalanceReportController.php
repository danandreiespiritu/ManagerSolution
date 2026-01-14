<?php

namespace App\Http\Controllers;

use App\Models\TrialBalanceReport;
use App\Repositories\TrialBalanceReport\ITrialBalanceReportRepository;
use App\Services\GeneralLedgerService;
use Illuminate\Http\Request;

class TrialBalanceReportController extends Controller
{
    public function __construct(
        protected ITrialBalanceReportRepository $repo,
        protected GeneralLedgerService $ledger
    ) {
    }

    public function index(Request $request)
    {
        $reports = $this->repo->getAll($request->user()->id);
        return view('reports.generalLedger.trialbalance.trialbalanceIndex', compact('reports'));
    }

    public function create()
    {
        return view('reports.generalLedger.trialbalance.trialbalanceCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'accounting_method' => 'required|string',
        ]);

        $report = $this->repo->create($request->user()->id, [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'date_from' => $data['from_date'],
            'date_to' => $data['to_date'],
            'accounting_method' => $data['accounting_method'],
        ]);

        return redirect()->route('reports.general-ledger.trial-balance.show', $report->id)
            ->with('success', 'Trial balance report created.');
    }

    public function show(TrialBalanceReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);

        $from = $report->date_from?->format('Y-m-d') ?? null;
        $to = $report->date_to?->format('Y-m-d') ?? null;

        $tb = $this->ledger->getTrialBalance((int) $businessId, $from, $to);

        // build rows suitable for the view
        $rows = collect($tb['accounts'] ?? [])->map(function ($a) {
            $bal = (float) ($a->balance ?? 0.0);
            $debit = $bal >= 0 ? $bal : 0.0;
            $credit = $bal < 0 ? abs($bal) : 0.0;

            return (object) [
                'code' => $a->account_code ?? null,
                'name' => $a->account_name ?? null,
                'debit' => $debit,
                'credit' => $credit,
                'status' => (abs($debit - $credit) <= 0.01) ? 'Balanced' : 'Unbalanced',
            ];
        });

        $totalDebit = $rows->sum('debit');
        $totalCredit = $rows->sum('credit');

        return view('reports.generalLedger.trialbalance.trialbalanceShow', compact('report','rows','totalDebit','totalCredit'));
    }
}
