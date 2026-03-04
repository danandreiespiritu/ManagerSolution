<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierSummaryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplierStatementUnpaidController extends Controller
{
    public function index(Request $request)
    {
        $reports = SupplierSummaryReport::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(15);
        return view('reports.suppliersummary.supplierUnpaidInvoices.UnpaidInvoicesIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.suppliersummary.supplierUnpaidInvoices.UnpaidInvoicesCreate');
    }

    public function generate(Request $request)
    {
        $data = $request->validate(['Date' => ['required','date']]);
        $date = Carbon::parse($data['Date'])->toDateString();

        $business = app('currentBusiness');

        $report = SupplierSummaryReport::create([
            'user_id' => Auth::id(),
            'business_id' => $business->id ?? null,
            'title' => 'Supplier unpaid bills as of '.$date,
            'from_date' => $date,
            'to_date' => $date,
        ]);

        return redirect()->route('reports.suppliers.statement-unpaid.show', $report);
    }

    public function show(Request $request, SupplierSummaryReport $report)
    {
        $business = app('currentBusiness');
        $statementDate = $report->from_date ?? now();

        $billsQuery = SupplierBill::where('business_id', $business->id ?? null)
            ->whereDate('bill_date', '<=', $statementDate)
            ->with('supplier');

        if ($request->has('supplier_id')) {
            $billsQuery->where('supplier_id', $request->get('supplier_id'));
        }

        $bills = $billsQuery->get()->map(function ($b) use ($statementDate) {
            $b->issue_date = $b->bill_date;
            $b->grand_total = $b->total_amount;
            $b->balance_due = $b->balanceDue();
            $due = $b->due_date;
            $b->overdue_days = $due ? max(0, $statementDate->diffInDays($due)) : 0;
            $b->payment_status = $b->balance_due <= 0 ? 'Paid' : 'Unpaid';
            return $b;
        })->filter(function ($b) {
            return ($b->balance_due ?? 0) > 0;
        })->values();

        $aging = [
            'current' => 0,
            'bucket_1_30' => 0,
            'bucket_31_60' => 0,
            'bucket_61_90' => 0,
            'bucket_90_plus' => 0,
            'total' => 0,
        ];

        foreach ($bills as $b) {
            $bd = (int) ($b->overdue_days ?? 0);
            $amt = (float) ($b->balance_due ?? 0);
            if ($bd <= 0) $aging['current'] += $amt;
            elseif ($bd <= 30) $aging['bucket_1_30'] += $amt;
            elseif ($bd <= 60) $aging['bucket_31_60'] += $amt;
            elseif ($bd <= 90) $aging['bucket_61_90'] += $amt;
            else $aging['bucket_90_plus'] += $amt;
            $aging['total'] += $amt;
        }

        $supplier = null;
        if ($request->has('supplier_id')) {
            $supplier = Supplier::find($request->get('supplier_id'));
        }

        return view('reports.suppliersummary.supplierUnpaidInvoices.UnpaidInvoicesShow', compact('report','bills','aging','statementDate','supplier'));
    }

    public function export(Request $request, SupplierSummaryReport $report)
    {
        $business = app('currentBusiness');
        $statementDate = $report->from_date ?? now();

        $billsQuery = SupplierBill::where('business_id', $business->id ?? null)
            ->whereDate('bill_date', '<=', $statementDate)
            ->with('supplier');

        if ($request->has('supplier_id')) {
            $billsQuery->where('supplier_id', $request->get('supplier_id'));
        }

        $bills = $billsQuery->get()->filter(function ($b) use ($statementDate) {
            return ($b->balanceDue() ?? 0) > 0;
        });

        $filename = 'supplier-unpaid-'.$report->id.'.csv';

        $response = new StreamedResponse(function () use ($bills, $statementDate) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date','Bill','Supplier','Bill total','Balance due','Overdue days']);
            foreach ($bills as $b) {
                $issue = optional($b->bill_date)->format('Y-m-d');
                $ref = $b->bill_number ?? ('BIL-'.$b->id);
                $sup = optional($b->supplier)->supplier_name ?? '';
                $total = number_format((float)($b->total_amount ?? 0), 2, '.', '');
                $balance = number_format((float)($b->balanceDue() ?? 0), 2, '.', '');
                $overdue = $b->due_date ? max(0, $statementDate->diffInDays($b->due_date)) : 0;
                fputcsv($handle, [$issue, $ref, $sup, $total, $balance, $overdue]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        return $response;
    }
}
