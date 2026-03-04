<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralLedgerSummaryReport;
use App\Repositories\GeneralLedgerSummaryReport\IGeneralLedgerSummaryReportRepository;
use Illuminate\Support\Facades\DB;

class GeneralLedgerTransactionsController extends Controller
{
    public function __construct(protected IGeneralLedgerSummaryReportRepository $repo)
    {
    }

    public function index(Request $request)
    {
        $reports = $this->repo->getAll($request->user()->id);
        return view('reports.generalLedger.generalLedgerSummary.generalLedgerTransactionsIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $accounts = DB::table('chart_of_accounts')
            ->where('user_id', $request->user()->id)
            ->when($businessId, fn($q) => $q->where('business_id', $businessId))
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereRaw("LOWER(COALESCE(account_type,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(classification,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(account_group,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(`group`,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(cash_flow_category,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%bank%'")
                    ->orWhereRaw("LOWER(COALESCE(account_name,'')) LIKE '%petty%'");
            })
            ->orderBy('account_code')
            ->get()->map(function ($a) {
            return (object) ['id' => $a->id, 'name' => $a->account_name ?? $a->name ?? null, 'code' => $a->account_code ?? $a->code ?? null];
        });

        return view('reports.generalLedger.generalLedgerSummary.generalLedgerSummaryCreate', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'account_id' => 'nullable|integer',
        ]);

        $report = $this->repo->create($request->user()->id, [
            'business_id' => $request->user()->current_business_id ?? null,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'account_id' => $data['account_id'] ?? null,
        ]);

        return redirect()->route('reports.general-ledger.transactions.showSaved', $report->id)->with('success', 'General ledger transactions report created.');
    }

    public function showSaved(GeneralLedgerSummaryReport $report)
    {
        if ((int) $report->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $from = $report->from_date?->format('Y-m-d') ?? null;
        $to = $report->to_date?->format('Y-m-d') ?? null;

        $cashAccountIds = DB::table('chart_of_accounts as a')
            ->when($businessId, fn($q) => $q->where('a.business_id', $businessId))
            ->where('a.is_active', 1)
            ->where(function ($q) {
                $q->whereRaw("LOWER(COALESCE(a.account_type,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.classification,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_group,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.group,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.cash_flow_category,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%bank%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%petty%'");
            })
            ->pluck('a.id')
            ->map(fn($id) => (int)$id)
            ->all();

        if (empty($cashAccountIds)) {
            $entries = collect();
            $accountNames = [];
            $accountCodes = [];
            $entryStatus = [];
            $totalDebit = 0.0;
            $totalCredit = 0.0;
            $balance = 0.0;
            $accountFilterName = null;

            return view('reports.generalLedger.generalLedgerSummary.generalLedgerTransactionsShow', compact('report','from','to','entries','accountNames','accountCodes','entryStatus','totalDebit','totalCredit','balance','accountFilterName'));
        }

        $query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->select(
                'l.*',
                'e.id as entry_id',
                DB::raw('e.entry_date as date'),
                DB::raw('l.debit_amount as debit'),
                DB::raw('l.credit_amount as credit'),
                DB::raw('COALESCE(l.description, e.description) as narration'),
                DB::raw('COALESCE(e.reference_type, e.reference_id, e.id) as reference')
            )
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->whereIn('l.account_id', $cashAccountIds);

        if ($from) $query->where('e.entry_date', '>=', $from);
        if ($to) $query->where('e.entry_date', '<=', $to);
        if ($report->account_id) $query->where('l.account_id', $report->account_id);

        $entries = $query->orderBy('e.entry_date')->orderBy('l.id')->get();

        $accountIds = $entries->pluck('account_id')->filter()->unique()->values()->all();
        $acctRows = DB::table('chart_of_accounts')->whereIn('id', $accountIds)->get()->keyBy('id');

        $accountNames = [];
        $accountCodes = [];
        foreach ($acctRows as $id => $a) {
            $accountNames[$id] = $a->account_name ?? $a->name ?? null;
            $accountCodes[$id] = $a->account_code ?? $a->code ?? null;
        }

        // compute per-entry status
        $entryStatus = [];
        foreach ($entries->groupBy('entry_id') as $entryId => $group) {
            $sumDebit = (float) $group->sum('debit_amount');
            $sumCredit = (float) $group->sum('credit_amount');
            $entryStatus[$entryId] = (abs($sumDebit - $sumCredit) <= 0.01) ? 'Balanced' : 'Unbalanced';
        }

        $totalDebit = (float) $entries->sum('debit_amount');
        $totalCredit = (float) $entries->sum('credit_amount');
        $balance = $totalDebit - $totalCredit;

        $accountFilterName = $report->account_id ? ($accountNames[$report->account_id] ?? null) : null;

        return view('reports.generalLedger.generalLedgerSummary.generalLedgerTransactionsShow', compact('report','from','to','entries','accountNames','accountCodes','entryStatus','totalDebit','totalCredit','balance','accountFilterName'));
    }

    public function export(GeneralLedgerSummaryReport $report)
    {
        // simple CSV export of entries
        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);
        $from = $report->from_date?->format('Y-m-d') ?? null;
        $to = $report->to_date?->format('Y-m-d') ?? null;


        $cashAccountIds = DB::table('chart_of_accounts as a')
            ->when($businessId, fn($q) => $q->where('a.business_id', $businessId))
            ->where('a.is_active', 1)
            ->where(function ($q) {
                $q->whereRaw("LOWER(COALESCE(a.account_type,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.classification,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_group,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.group,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.cash_flow_category,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%cash%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%bank%'")
                    ->orWhereRaw("LOWER(COALESCE(a.account_name,'')) LIKE '%petty%'");
            })
            ->pluck('a.id')
            ->map(fn($id) => (int)$id)
            ->all();

        if (empty($cashAccountIds)) {
            $entries = collect();
        } else {
            $query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->select(
                'l.*',
                'e.id as entry_id',
                DB::raw('e.entry_date as date'),
                DB::raw('l.debit_amount as debit'),
                DB::raw('l.credit_amount as credit'),
                DB::raw('COALESCE(l.description, e.description) as narration'),
                DB::raw('COALESCE(e.reference_type, e.reference_id, e.id) as reference')
            )
            ->when($businessId, fn($q) => $q->where('l.business_id', $businessId))
            ->whereIn('l.account_id', $cashAccountIds);

            if ($from) $query->where('e.entry_date', '>=', $from);
            if ($to) $query->where('e.entry_date', '<=', $to);
            if ($report->account_id) $query->where('l.account_id', $report->account_id);

            $entries = $query->orderBy('e.entry_date')->orderBy('l.id')->get();
        }

        $filename = 'gl-transactions-' . now()->format('YmdHis') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\"",];

        $callback = function () use ($entries) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['entry_id','entry_date','account_id','debit','credit','description']);
            foreach ($entries as $e) {
                fputcsv($out, [ $e->entry_id, $e->entry_date, $e->account_id, $e->debit_amount, $e->credit_amount, $e->description ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
