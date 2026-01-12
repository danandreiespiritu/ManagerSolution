<?php

namespace App\Repositories\ChartofAccounts;

use App\Models\ChartofAccounts;

class ChartofAccountsRepository implements IChartofAccountsRepository
{
    public function getByBusiness(?int $businessId)
    {
        $q = ChartofAccounts::query();
        if ($businessId) {
            $q->where('business_id', $businessId);
        }

        return $q->whereNotNull('account_name')
            ->where('account_name', '<>', '')
            ->orderBy('account_code')
            ->get();
    }
}
