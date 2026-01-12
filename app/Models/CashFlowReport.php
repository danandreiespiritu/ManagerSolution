<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class CashFlowReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'description',
        'method',
        'from',
        'to',
        'column_label',
        'comparatives',
    ];

    protected $casts = [
        'from' => 'date',
        'to' => 'date',
        'comparatives' => 'array',
    ];
}
