<?php
namespace App\Repositories\Business;

use App\Models\Business as BusinessModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IBusinessRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function create(int $userId, array $data): BusinessModel;

    public function getAll(int $userId): Collection;

    public function getById(int $userId, int $id): ?BusinessModel;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
