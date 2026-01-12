<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class SupplierDebitNoteBill extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $table = 'supplier_debit_note_bills';

    protected $fillable = [
        'user_id', 'business_id', 'supplier_debit_note_id', 'supplier_bill_id', 'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function note()
    {
        return $this->belongsTo(SupplierDebitNote::class, 'supplier_debit_note_id');
    }

    public function bill()
    {
        return $this->belongsTo(SupplierBill::class, 'supplier_bill_id');
    }
}
