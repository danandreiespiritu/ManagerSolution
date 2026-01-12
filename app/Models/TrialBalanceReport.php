<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class TrialBalanceReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'description',
        'method',
        'from_date',
        'to_date',
        'comparative_columns',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'comparative_columns' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
