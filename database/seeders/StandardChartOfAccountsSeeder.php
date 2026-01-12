<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\StandardCoaService;

class StandardChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var StandardCoaService $svc */
        $svc = app(StandardCoaService::class);
        $svc->ensureForAllBusinesses();
    }
}
