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
        $payments = (float) $this->invoicePayments()->sum('amount');
        $creditAlloc = 0.0;
        if (method_exists($this, 'creditAllocations')) {
            $creditAlloc = (float) $this->creditAllocations()->sum('amount');
        }

        // Credits reduce outstanding (so they increase applied amount)
        return (float) ($payments + $creditAlloc);
    }

    public function creditAllocations()
    {
        return $this->hasMany(CustomerCreditNoteInvoice::class, 'customer_invoice_id');
    }

    public function outstandingAmount(): float
    {
        return (float) $this->total_amount - $this->appliedAmount();
    }

    // Backwards-compatible alias used in views/controllers
    public function balanceDue(): float
    {
        return $this->outstandingAmount();
    }
}
