<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class CustomerCreditNoteInvoice extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $table = 'customer_credit_note_invoices';

    protected $fillable = [
        'user_id', 'business_id', 'customer_credit_note_id', 'customer_invoice_id', 'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CustomerCreditNote::class, 'customer_credit_note_id');
    }

    public function invoice()
    {
        return $this->belongsTo(CustomerInvoice::class, 'customer_invoice_id');
    }
}
