<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class GeneralLedgerTransactionReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'description',
        'from_date',
        'to_date',
        'account_id',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];
}
