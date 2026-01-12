<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class SupplierBill extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','bill_number','supplier_id','bill_date','due_date','total_amount','status','expense_account_id','ap_account_id'];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function expenseAccount()
    {
        return $this->belongsTo(ChartofAccounts::class, 'expense_account_id');
    }

    public function apAccount()
    {
        return $this->belongsTo(ChartofAccounts::class, 'ap_account_id');
    }

    public function billPayments()
    {
        return $this->hasMany(SupplierBillPayment::class, 'supplier_bill_id');
    }

    public function payments()
    {
        return $this->belongsToMany(SupplierPayment::class, 'supplier_bill_payments', 'supplier_bill_id', 'supplier_payment_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function appliedAmount(): float
    {
        $payments = (float) $this->billPayments()->sum('amount');
        $creditAlloc = 0.0;
        $debitAlloc = 0.0;

        if (method_exists($this, 'creditNoteAllocations')) {
            $creditAlloc = (float) $this->creditNoteAllocations()->sum('amount');
        }
        if (method_exists($this, 'debitNoteAllocations')) {
            $debitAlloc = (float) $this->debitNoteAllocations()->sum('amount');
        }

        // Credits reduce outstanding (so they increase applied amount), debits increase outstanding (so they reduce applied amount)
        return (float) ($payments + $creditAlloc - $debitAlloc);
    }

    public function balanceDue(): float
    {
        return (float) $this->total_amount - $this->appliedAmount();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creditNoteAllocations()
    {
        return $this->hasMany(SupplierCreditNoteBill::class, 'supplier_bill_id');
    }

    public function debitNoteAllocations()
    {
        return $this->hasMany(SupplierDebitNoteBill::class, 'supplier_bill_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
