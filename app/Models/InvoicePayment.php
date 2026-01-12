<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class InvoicePayment extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','payment_id','customer_invoice_id','amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(CustomerInvoice::class, 'customer_invoice_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
