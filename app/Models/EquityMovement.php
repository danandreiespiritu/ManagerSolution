<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class EquityMovement extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $table = 'equity_movements';

    protected $fillable = ['user_id','business_id','movement_date','account_id','amount','movement_type','accounting_period_id'];

    protected $casts = ['movement_date' => 'date','amount' => 'decimal:2'];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function accountingPeriod()
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
