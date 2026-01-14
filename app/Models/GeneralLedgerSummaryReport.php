<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class GeneralLedgerSummaryReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'title',
        'user_id',
        'business_id',
        'description',
        'account_id',
        'from_date',
        'to_date',
        'show_codes',
        'exclude_zero',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'show_codes' => 'boolean',
        'exclude_zero' => 'boolean',
    ];
}
