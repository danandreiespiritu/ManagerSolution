<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class Budget extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','account_id','accounting_period_id','budget_amount'];

    protected $casts = ['budget_amount' => 'decimal:2'];

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
