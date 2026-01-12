<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class Supplier extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','supplier_code','supplier_name','email','is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function bills()
    {
        return $this->hasMany(SupplierBill::class);
    }

    public function debitNotes()
    {
        return $this->hasMany(SupplierDebitNote::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->supplier_name ?? '');
    }

    public function getCodeAttribute(): ?string
    {
        $code = $this->supplier_code ?? null;
        return $code !== '' ? $code : null;
    }
}
