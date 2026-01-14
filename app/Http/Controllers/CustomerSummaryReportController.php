<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCreditNote;
use App\Models\CustomerInvoice;
use App\Models\CustomerSummaryReport;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerSummaryReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = CustomerSummaryReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reports.customersummary.customerIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.customersummary.customerCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = CustomerSummaryReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'title' => 'Customer Summary',
            'description' => $data['description'] ?? null,
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
        ]);

        return redirect()->route('reports.customer-summary.show', $report)->with('success', 'Customer summary created.');
    }

    public function show(Request $request, CustomerSummaryReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);

        $from = $report->from_date?->format('Y-m-d') ?? null;
        $to = $report->to_date?->format('Y-m-d') ?? null;

        $data = [];

        $customers = Customer::where('business_id', $businessId)->get();

        foreach ($customers as $c) {
            // Opening balances: transactions before the 'from' date
            $openingInvoices = $from ? CustomerInvoice::where('business_id', $businessId)
                ->where('customer_id', $c->id)
                ->where('invoice_date', '<', $from)
                ->sum('total_amount') : 0;

            $openingPayments = $from ? InvoicePayment::whereHas('payment', function ($q) use ($from, $businessId) {
                $q->where('payment_date', '<', $from)->where('business_id', $businessId);
            })->whereHas('invoice', function ($q) use ($c) {
                $q->where('customer_id', $c->id);
            })->sum('amount') : 0;

            $openingCredits = $from ? CustomerCreditNote::where('business_id', $businessId)
                ->where('customer_id', $c->id)
                ->where('credit_date', '<', $from)
                ->sum('total_amount') : 0;

            $opening = (float)$openingInvoices - (float)$openingPayments - (float)$openingCredits;

            // Closing balances: transactions up to and including the 'to' date
            $closingInvoices = $to ? CustomerInvoice::where('business_id', $businessId)
                ->where('customer_id', $c->id)
                ->where('invoice_date', '<=', $to)
                ->sum('total_amount') : 0;

            $closingPayments = $to ? InvoicePayment::whereHas('payment', function ($q) use ($to, $businessId) {
                $q->where('payment_date', '<=', $to)->where('business_id', $businessId);
            })->whereHas('invoice', function ($q) use ($c) {
                $q->where('customer_id', $c->id);
            })->sum('amount') : 0;

            $closingCredits = $to ? CustomerCreditNote::where('business_id', $businessId)
                ->where('customer_id', $c->id)
                ->where('credit_date', '<=', $to)
                ->sum('total_amount') : 0;

            $closing = (float)$closingInvoices - (float)$closingPayments - (float)$closingCredits;

            $data[] = [
                'customer_code' => $c->customer_code ?? '',
                'customer_name' => $c->customer_name ?? '',
                'opening_balance' => round($opening, 2),
                'closing_balance' => round($closing, 2),
            ];
        }

        return view('reports.customersummary.customerShow', compact('report', 'data'));
    }
}
