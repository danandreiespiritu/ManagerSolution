<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class ChartofAccounts extends Model
{
    use BelongsToBusiness;
    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'user_id',
        'business_id',
        'account_type', // e.g., 'BlAccount', 'PlAccount'
        'classification', // asset, liability, equity, income, expense, contra-income, contra-asset
        'account_name', // e.g., 'BlAccountName', 'PlAccountName'
        'account_code', // e.g., 'BlAccountCode', 'PlAccountCode'
        'account_group', // e.g., 'BlAccountGroup', 'PlAccountGroup'
        'group', // e.g., 'BlGroup', 'PlGroup'
        'group_category', // e.g., 'Bl Group Category', 'Pl Group Category'
        'cash_flow_category', // e.g., 'Cash Flow Statement Category'
        'is_active',
        'is_control_account',
    ];
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

    

