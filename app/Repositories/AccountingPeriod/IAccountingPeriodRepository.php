<?php
namespace App\Repositories\AccountingPeriod;

use App\Models\AccountingPeriod as AccountingPeriodModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IAccountingPeriodRepository
{
    public function create(array $payload): AccountingPeriodModel;

    public function getById(int $id): ?AccountingPeriodModel;

    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAllForUser(int $userId): Collection;

    public function update(int $id, array $payload): ?AccountingPeriodModel;

    public function delete(int $id): bool;
}
