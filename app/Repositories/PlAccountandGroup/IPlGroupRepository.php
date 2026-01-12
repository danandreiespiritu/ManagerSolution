<?php
namespace App\Repositories\PlAccountandGroup;

use App\Models\ChartofAccounts as PlGroupModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IPlGroupRepository
{
    public function paginate(int $userId, int $perPage = 15, ?int $businessId = null): LengthAwarePaginator;

    public function create(int $userId, array $data, ?int $businessId = null): PlGroupModel;

    public function getAll(int $userId, ?int $businessId = null): Collection;

    public function getById(int $userId, int $id, ?int $businessId = null): ?PlGroupModel;

    public function update(int $userId, int $id, array $data, ?int $businessId = null): bool;

    public function delete(int $userId, int $id, ?int $businessId = null): bool;

    public function getCategory(int $id, int $userId, ?int $businessId = null): ?PlGroupModel;
}
