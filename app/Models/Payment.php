<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class Payment extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','payment_date','amount','payment_type','customer_id','supplier_id','cash_account_id','reference'];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'cash_account_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(CustomerInvoice::class, 'invoice_payments', 'payment_id', 'customer_invoice_id')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
