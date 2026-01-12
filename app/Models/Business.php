<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $table = 'businesses';

    protected $fillable = [
        'user_id',
        'business_name',
        'phone',
        'email',
        'address_line1',
        'city',
        'province',
        'country',
        'postal_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return (string) ($this->business_name ?? '');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chartOfAccounts(): HasMany
    {
        return $this->hasMany(ChartofAccounts::class);
    }
}
