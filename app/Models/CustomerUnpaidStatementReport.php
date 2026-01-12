<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class CustomerUnpaidStatementReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'statement_date',
    ];

    protected $casts = [
        'statement_date' => 'date',
    ];
}
