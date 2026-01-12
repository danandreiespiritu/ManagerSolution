<?php

namespace App\Repositories\ProfitAndLossReport;

use App\Models\ProfitAndLossReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IProfitAndLossReportRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAll(int $userId): Collection;

    public function getById(int $userId, int $id): ?ProfitAndLossReport;

    public function create(int $userId, array $data): ProfitAndLossReport;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
