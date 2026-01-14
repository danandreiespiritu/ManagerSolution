<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierBill;
use App\Models\SupplierBillPayment;
use App\Models\SupplierCreditNote;
use App\Models\SupplierSummaryReport;
use Illuminate\Http\Request;

class SupplierSummaryReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = SupplierSummaryReport::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('reports.suppliersummary.supplierIndex', compact('reports'));
    }

    public function create(Request $request)
    {
        return view('reports.suppliersummary.supplierCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $businessId = app()->bound('currentBusiness') ? app('currentBusiness')->id : null;

        $report = SupplierSummaryReport::create([
            'user_id' => $request->user()->id,
            'business_id' => $businessId,
            'title' => 'Supplier Summary',
            'description' => $data['description'] ?? null,
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
        ]);

        return redirect()->route('reports.supplier-summary.show', $report)->with('success', 'Supplier summary created.');
    }

    public function show(Request $request, SupplierSummaryReport $report)
    {
        if ((int)$report->user_id !== (int)auth()->id()) abort(403);

        $businessId = $report->business_id ?? (app()->bound('currentBusiness') ? app('currentBusiness')->id : null);

        $from = $report->from_date?->format('Y-m-d') ?? null;
        $to = $report->to_date?->format('Y-m-d') ?? null;

        $data = [];

        $suppliers = Supplier::where('business_id', $businessId)->get();

        foreach ($suppliers as $s) {
            $openingBills = $from ? SupplierBill::where('business_id', $businessId)
                ->where('supplier_id', $s->id)
                ->where('bill_date', '<', $from)
                ->sum('total_amount') : 0;

            $openingPayments = $from ? SupplierBillPayment::whereHas('payment', function ($q) use ($from, $businessId) {
                $q->where('payment_date', '<', $from)->where('business_id', $businessId);
            })->whereHas('bill', function ($q) use ($s) {
                $q->where('supplier_id', $s->id);
            })->sum('amount') : 0;

            $openingCredits = $from ? SupplierCreditNote::where('business_id', $businessId)
                ->where('supplier_id', $s->id)
                ->where('credit_date', '<', $from)
                ->sum('total_amount') : 0;

            $opening = (float)$openingBills - (float)$openingPayments - (float)$openingCredits;

            $closingBills = $to ? SupplierBill::where('business_id', $businessId)
                ->where('supplier_id', $s->id)
                ->where('bill_date', '<=', $to)
                ->sum('total_amount') : 0;

            $closingPayments = $to ? SupplierBillPayment::whereHas('payment', function ($q) use ($to, $businessId) {
                $q->where('payment_date', '<=', $to)->where('business_id', $businessId);
            })->whereHas('bill', function ($q) use ($s) {
                $q->where('supplier_id', $s->id);
            })->sum('amount') : 0;

            $closingCredits = $to ? SupplierCreditNote::where('business_id', $businessId)
                ->where('supplier_id', $s->id)
                ->where('credit_date', '<=', $to)
                ->sum('total_amount') : 0;

            $closing = (float)$closingBills - (float)$closingPayments - (float)$closingCredits;

            $data[] = [
                'supplier_code' => $s->supplier_code ?? '',
                'supplier_name' => $s->supplier_name ?? '',
                'opening_balance' => round($opening, 2),
                'closing_balance' => round($closing, 2),
            ];
        }

        return view('reports.suppliersummary.supplierShow', compact('report', 'data'));
    }
}
