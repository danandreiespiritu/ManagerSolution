<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Models\CustomerSummaryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerStatementUnpaidController extends Controller
{
    public function index(Request $request)
    {
        $reports = CustomerSummaryReport::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(15);
        return view('reports.customersummary.customerUnpaidInvoices.UnpaidInvoicesIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.customersummary.customerUnpaidInvoices.UnpaidInvoicesCreate');
    }

    public function generate(Request $request)
    {
        $data = $request->validate(['Date' => ['required','date']]);
        $date = Carbon::parse($data['Date'])->toDateString();

        $business = app('currentBusiness');

        $report = CustomerSummaryReport::create([
            'user_id' => Auth::id(),
            'business_id' => $business->id ?? null,
            'title' => 'Customer unpaid invoices as of '.$date,
            'from_date' => $date,
            'to_date' => $date,
        ]);

        return redirect()->route('reports.customers.statement-unpaid.show', $report);
    }

    public function show(Request $request, CustomerSummaryReport $report)
    {
        $business = app('currentBusiness');
        $statementDate = $report->from_date ?? now();

        $invoicesQuery = CustomerInvoice::where('business_id', $business->id ?? null)
            ->whereDate('invoice_date', '<=', $statementDate)
            ->with('customer');

        if ($request->has('customer_id')) {
            $invoicesQuery->where('customer_id', $request->get('customer_id'));
        }

        $invoices = $invoicesQuery->get()->map(function ($inv) use ($statementDate) {
            $inv->issue_date = $inv->invoice_date;
            $inv->grand_total = $inv->total_amount;
            $inv->balance_due = $inv->balanceDue();
            $due = $inv->due_date;
            $inv->overdue_days = $due ? max(0, $statementDate->diffInDays($due)) : 0;
            $inv->payment_status = $inv->balance_due <= 0 ? 'Paid' : 'Unpaid';
            return $inv;
        })->filter(function ($inv) {
            return ($inv->balance_due ?? 0) > 0;
        })->values();

        $aging = [
            'current' => 0,
            'bucket_1_30' => 0,
            'bucket_31_60' => 0,
            'bucket_61_90' => 0,
            'bucket_90_plus' => 0,
            'total' => 0,
        ];

        foreach ($invoices as $inv) {
            $bd = (int) ($inv->overdue_days ?? 0);
            $amt = (float) ($inv->balance_due ?? 0);
            if ($bd <= 0) $aging['current'] += $amt;
            elseif ($bd <= 30) $aging['bucket_1_30'] += $amt;
            elseif ($bd <= 60) $aging['bucket_31_60'] += $amt;
            elseif ($bd <= 90) $aging['bucket_61_90'] += $amt;
            else $aging['bucket_90_plus'] += $amt;
            $aging['total'] += $amt;
        }

        $customer = null;
        if ($request->has('customer_id')) {
            $customer = Customer::find($request->get('customer_id'));
        }

        return view('reports.customersummary.customerUnpaidInvoices.UnpaidInvoicesShow', compact('report','invoices','aging','statementDate','customer'));
    }

    public function export(Request $request, CustomerSummaryReport $report)
    {
        $business = app('currentBusiness');
        $statementDate = $report->from_date ?? now();

        $invoicesQuery = CustomerInvoice::where('business_id', $business->id ?? null)
            ->whereDate('invoice_date', '<=', $statementDate)
            ->with('customer');

        if ($request->has('customer_id')) {
            $invoicesQuery->where('customer_id', $request->get('customer_id'));
        }

        $invoices = $invoicesQuery->get()->filter(function ($inv) use ($statementDate) {
            return ($inv->balanceDue() ?? 0) > 0;
        });

        $filename = 'customer-unpaid-'.$report->id.'.csv';

        $response = new StreamedResponse(function () use ($invoices, $statementDate) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date','Invoice','Customer','Invoice total','Balance due','Overdue days']);
            foreach ($invoices as $inv) {
                $issue = optional($inv->invoice_date)->format('Y-m-d');
                $ref = $inv->invoice_number ?? ('INV-'.$inv->id);
                $cust = optional($inv->customer)->name ?? '';
                $total = number_format((float)($inv->total_amount ?? 0), 2, '.', '');
                $balance = number_format((float)($inv->balanceDue() ?? 0), 2, '.', '');
                $overdue = $inv->due_date ? max(0, $statementDate->diffInDays($inv->due_date)) : 0;
                fputcsv($handle, [$issue, $ref, $cust, $total, $balance, $overdue]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        return $response;
    }
}
