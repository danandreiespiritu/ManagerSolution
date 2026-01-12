<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class SupplierPayment extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'supplier_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference',
        'cash_account_id',
        'ap_account_id',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(ChartofAccounts::class, 'cash_account_id');
    }

    public function apAccount()
    {
        return $this->belongsTo(ChartofAccounts::class, 'ap_account_id');
    }

    protected $appends = ['payment_method_label'];

    // Normalize and store payment method as a code (e.g. bank_transfer)
    public function setPaymentMethodAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['payment_method'] = '';
            return;
        }

        $map = [
            'bank_transfer' => 'bank_transfer', 'bank transfer' => 'bank_transfer', 'Bank transfer' => 'bank_transfer',
            'cash' => 'cash', 'Cash' => 'cash',
            'cheque' => 'cheque', 'Cheque' => 'cheque',
            'card' => 'card', 'Card' => 'card',
            'eft' => 'eft', 'EFT' => 'eft',
            'other' => 'other', 'Other' => 'other',
        ];

        $v = trim((string) $value);
        $lower = strtolower($v);
        $this->attributes['payment_method'] = $map[$v] ?? ($map[$lower] ?? $v);
    }

    // Human-readable label for the payment method
    public function getPaymentMethodLabelAttribute(): string
    {
        $map = [
            'bank_transfer' => 'Bank transfer',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'card' => 'Card',
            'eft' => 'EFT',
            'other' => 'Other',
        ];

        $code = $this->attributes['payment_method'] ?? '';
        return $map[$code] ?? ($code ?: '—');
    }

    public function billPayments()
    {
        return $this->hasMany(SupplierBillPayment::class);
    }

    public function bills()
    {
        return $this->belongsToMany(SupplierBill::class, 'supplier_bill_payments', 'supplier_payment_id', 'supplier_bill_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function allocatedAmount(): float
    {
        return (float) $this->billPayments()->sum('amount');
    }

    public function unallocatedAmount(): float
    {
        return (float) $this->amount - $this->allocatedAmount();
    }
}
