<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Support\Collection;

class BalanceSheetReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'date',
        'accounting_method',
        'layout',
        'description',
        'columns',
        'footer',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getColumnsAttribute($value): Collection
    {
        if (empty($value)) {
            return collect();
        }

        $decoded = json_decode($value);
        if (! is_array($decoded)) {
            return collect();
        }

        return collect($decoded);
    }

    public function setColumnsAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['columns'] = null;
            return;
        }

        $this->attributes['columns'] = json_encode($value);
    }
}
