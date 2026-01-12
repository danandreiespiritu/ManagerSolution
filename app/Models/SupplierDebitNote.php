<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class SupplierDebitNote extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','debit_note_number','supplier_id','debit_date','total_amount','reason','status','expense_account_id','ap_account_id'];

    protected $casts = [
        'debit_date' => 'date',
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function bills()
    {
        return $this->belongsToMany(SupplierBill::class, 'supplier_debit_note_bills', 'supplier_debit_note_id', 'supplier_bill_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function allocations()
    {
        return $this->hasMany(SupplierDebitNoteBill::class, 'supplier_debit_note_id');
    }

    public function allocatedAmount(): float
    {
        return (float) $this->allocations()->sum('amount');
    }
}
