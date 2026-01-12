<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class SupplierBillPayment extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $table = 'supplier_bill_payments';

    protected $fillable = [
        'user_id',
        'business_id',
        'supplier_payment_id',
        'supplier_bill_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(SupplierPayment::class, 'supplier_payment_id');
    }

    public function bill()
    {
        return $this->belongsTo(SupplierBill::class, 'supplier_bill_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
