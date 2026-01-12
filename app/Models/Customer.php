<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class Customer extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','customer_code','customer_name','email','is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function invoices()
    {
        return $this->hasMany(CustomerInvoice::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(CustomerCreditNote::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->customer_name ?? '');
    }

    public function getCodeAttribute(): ?string
    {
        $code = $this->customer_code ?? null;
        return $code !== '' ? $code : null;
    }
}
