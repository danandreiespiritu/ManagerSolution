<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class ChangesInEquityReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'description',
        'accounting_method',
        'from',
        'to',
        'column_label',
        'comparatives',
        'footer',
    ];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'comparatives' => 'array',
    ];
}
