<?php

namespace App\Http\Controllers;

use App\Models\CustomerTransactionStatementReport;
use App\Models\CustomerInvoice;
use App\Models\InvoicePayment;
use App\Models\CustomerCreditNote;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerStatementTransactionsController extends Controller
{
    public function index(Request $request)
    {
        $reports = CustomerTransactionStatementReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reports.customersummary.customerTransaction.customerTransactionIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.customersummary.customerTransaction.customerTransactionSummary');
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'from_date' => 'required|date',
            'date_today' => 'required|date',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = CustomerTransactionStatementReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'title' => 'Customer Statement (Transactions)',
            'from_date' => $data['from_date'],
            'to_date' => $data['date_today'],
        ]);

        return redirect()->route('reports.customers.statement-transactions.show', $report);
    }

    public function show(Request $request, CustomerTransactionStatementReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);

        $fromDate = $report->from_date?->format('Y-m-d');
        $toDate = $report->to_date?->format('Y-m-d');

        $entries = [];

        // Invoices
        $invoices = CustomerInvoice::where('business_id', $businessId)
            ->whereBetween('invoice_date', [$fromDate, $toDate])
            ->get();

        foreach ($invoices as $inv) {
            $entries[] = (object)[
                'date' => $inv->invoice_date,
                'type' => 'Invoice',
                'reference' => $inv->invoice_number ?? $inv->id,
                'description' => $inv->description ?? ($inv->invoice_number ?? 'Invoice'),
                'debit' => (float)($inv->total_amount ?? 0),
                'credit' => 0.0,
            ];
        }

        // Payments (applied to invoices)
        $invoicePayments = InvoicePayment::with('payment', 'invoice')
            ->whereHas('payment', function ($q) use ($businessId, $fromDate, $toDate) {
                $q->where('business_id', $businessId)->whereBetween('payment_date', [$fromDate, $toDate]);
            })->get();

        foreach ($invoicePayments as $ip) {
            $p = $ip->payment;
            $inv = $ip->invoice;
            $entries[] = (object)[
                'date' => $p->payment_date,
                'type' => 'Payment',
                'reference' => $p->reference ?? $p->id,
                'description' => 'Payment applied to ' . ($inv->invoice_number ?? 'invoice'),
                'debit' => 0.0,
                'credit' => (float)($ip->amount ?? 0),
            ];
        }

        // Credit notes
        $credits = CustomerCreditNote::where('business_id', $businessId)
            ->whereBetween('credit_date', [$fromDate, $toDate])
            ->get();

        foreach ($credits as $cn) {
            $entries[] = (object)[
                'date' => $cn->credit_date,
                'type' => 'Credit Note',
                'reference' => $cn->credit_note_number ?? $cn->id,
                'description' => $cn->reason ?? ($cn->credit_note_number ?? 'Credit Note'),
                'debit' => 0.0,
                'credit' => (float)($cn->total_amount ?? 0),
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
        $balance = $totalDebit - $totalCredit;

        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);

        $customer = null;

        return view('reports.customersummary.customerTransaction.customerTransactionShow', compact('report', 'entries', 'totalDebit', 'totalCredit', 'balance', 'from', 'to', 'customer'));
    }
}
