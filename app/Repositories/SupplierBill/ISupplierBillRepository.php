<?php

namespace App\Repositories\SupplierBill;

use App\Models\SupplierBill;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ISupplierBillRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getById(int $userId, int $id): ?SupplierBill;

    /**
     * Create a supplier bill without emitting model events (prevents duplicate auto-journals).
     */
    public function createQuietly(int $userId, array $data): SupplierBill;

    public function update(int $userId, int $id, array $data): bool;
    public function delete(int $userId, int $id): bool;
}
