<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class CustomerInvoice extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','invoice_number','customer_id','invoice_date','due_date','total_amount','status'];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'customer_invoice_id');
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'invoice_payments', 'customer_invoice_id', 'payment_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function appliedAmount(): float
    {
        return (float) $this->invoicePayments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return (float) $this->total_amount - $this->appliedAmount();
    }
}
