<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class ProfitAndLossActualVsBudgetReport extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $table = 'profit_and_loss_actual_vs_budget_reports';

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'date_from',
        'date_to',
        'accounting_method',
        'lines',
        'footer',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'lines' => 'array',
    ];
}
