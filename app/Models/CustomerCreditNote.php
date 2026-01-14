<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class CustomerCreditNote extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','credit_note_number','customer_id','credit_date','total_amount','reason'];

    protected $casts = [
        'credit_date' => 'date',
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

    public function invoices()
    {
        return $this->belongsToMany(CustomerInvoice::class, 'customer_credit_note_invoices', 'customer_credit_note_id', 'customer_invoice_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function allocations()
    {
        return $this->hasMany(CustomerCreditNoteInvoice::class, 'customer_credit_note_id');
    }

    public function allocatedAmount(): float
    {
        return (float) $this->allocations()->sum('amount');
    }
}
