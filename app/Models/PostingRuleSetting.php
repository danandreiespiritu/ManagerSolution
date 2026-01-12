<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class PostingRuleSetting extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['business_id','rule_name','enabled','config'];

    protected $casts = [
        'enabled' => 'boolean',
        'config' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
