<?php

namespace App\Repositories\ChartofAccounts;

interface IChartofAccountsRepository
{
    /**
     * Return accounts for a business excluding those without an account_name.
     * @param int|null $businessId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByBusiness(?int $businessId);
}
