<?php

namespace App\Repositories\TrialBalanceReport;

use App\Models\TrialBalanceReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ITrialBalanceReportRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAll(int $userId): Collection;

    public function getById(int $userId, int $id): ?TrialBalanceReport;

    public function create(int $userId, array $data): TrialBalanceReport;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
