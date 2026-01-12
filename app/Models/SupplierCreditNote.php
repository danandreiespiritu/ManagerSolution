<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class SupplierCreditNote extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'credit_note_number',
        'supplier_id',
        'credit_date',
        'total_amount',
        'reason',
        'status',
        'ap_account_id',
        'offset_account_id',
    ];

    protected $casts = [
        'credit_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function apAccount()
    {
        return $this->belongsTo(ChartofAccounts::class, 'ap_account_id');
    }

    public function offsetAccount()
    {
        return $this->belongsTo(ChartofAccounts::class, 'offset_account_id');
    }

    public function bills()
    {
        return $this->belongsToMany(SupplierBill::class, 'supplier_credit_note_bills', 'supplier_credit_note_id', 'supplier_bill_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function allocations()
    {
        return $this->hasMany(SupplierCreditNoteBill::class, 'supplier_credit_note_id');
    }

    public function allocatedAmount(): float
    {
        return (float) $this->allocations()->sum('amount');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
