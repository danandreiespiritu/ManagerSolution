<?php

namespace App\Repositories\Supplier;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ISupplierRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getById(int $userId, int $id): ?Supplier;
    public function create(int $userId, array $data): Supplier;
    public function update(int $userId, int $id, array $data): bool;
    public function delete(int $userId, int $id): bool;
}
