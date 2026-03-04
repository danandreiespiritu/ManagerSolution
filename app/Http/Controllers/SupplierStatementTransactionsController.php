<?php

namespace App\Http\Controllers;

use App\Models\SupplierBill;
use App\Models\SupplierBillPayment;
use App\Models\SupplierCreditNote;
use App\Models\SupplierTransactionStatementReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SupplierStatementTransactionsController extends Controller
{
    public function index(Request $request)
    {
        $reports = SupplierTransactionStatementReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reports.suppliersummary.supplierTransaction.supplierTransactionIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.suppliersummary.supplierTransaction.supplierTransactionSummary');
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'from_date' => 'required|date',
            'date_today' => 'required|date',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = SupplierTransactionStatementReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'title' => 'Supplier Statement (Transactions)',
            'from_date' => $data['from_date'],
            'to_date' => $data['date_today'],
        ]);

        return redirect()->route('reports.suppliers.statement-transactions.show', $report);
    }

    public function show(Request $request, SupplierTransactionStatementReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);

        $fromDate = $report->from_date?->format('Y-m-d');
        $toDate = $report->to_date?->format('Y-m-d');

        $entries = [];

        // Bills (credit)
        $bills = SupplierBill::where('business_id', $businessId)
            ->whereBetween('bill_date', [$fromDate, $toDate])
            ->get();

        foreach ($bills as $b) {
            $entries[] = (object)[
                'date' => $b->bill_date,
                'type' => 'Bill',
                'reference' => $b->bill_number ?? $b->id,
                'description' => $b->description ?? ($b->bill_number ?? 'Bill'),
                'debit' => 0.0,
                'credit' => (float)($b->total_amount ?? 0),
            ];
        }

        // Payments (applied to bills)
        $billPayments = SupplierBillPayment::with('payment', 'bill')
            ->whereHas('payment', function ($q) use ($businessId, $fromDate, $toDate) {
                $q->where('business_id', $businessId)->whereBetween('payment_date', [$fromDate, $toDate]);
            })->get();

        foreach ($billPayments as $bp) {
            $p = $bp->payment;
            $bill = $bp->bill;
            $entries[] = (object)[
                'date' => $p->payment_date,
                'type' => 'Payment',
                'reference' => $p->reference ?? $p->id,
                'description' => 'Payment applied to ' . ($bill->bill_number ?? 'bill'),
                'debit' => (float)($bp->amount ?? 0),
                'credit' => 0.0,
            ];
        }

        // Credit notes (debit)
        $credits = SupplierCreditNote::where('business_id', $businessId)
            ->whereBetween('credit_date', [$fromDate, $toDate])
            ->get();

        foreach ($credits as $cn) {
            $entries[] = (object)[
                'date' => $cn->credit_date,
                'type' => 'Credit Note',
                'reference' => $cn->credit_note_number ?? $cn->id,
                'description' => $cn->reason ?? ($cn->credit_note_number ?? 'Credit Note'),
                'debit' => (float)($cn->total_amount ?? 0),
                'credit' => 0.0,
            ];
        }

        // Sort entries by date ascending
        usort($entries, function ($a, $b) {
            $da = $a->date instanceof \Carbon\Carbon ? $a->date->timestamp : strtotime($a->date);
            $db = $b->date instanceof \Carbon\Carbon ? $b->date->timestamp : strtotime($b->date);
            return $da <=> $db;
        });

        $totalDebit = array_sum(array_map(fn($e) => $e->debit, $entries));
        $totalCredit = array_sum(array_map(fn($e) => $e->credit, $entries));
        // For suppliers, balance (amount owed) is credits minus debits
        $balance = $totalCredit - $totalDebit;

        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        $supplier = null;

        return view('reports.suppliersummary.supplierTransaction.supplierTransactionShow', compact('report', 'entries', 'totalDebit', 'totalCredit', 'balance', 'from', 'to', 'supplier'));
    }
}
