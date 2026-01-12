<?php

namespace App\Repositories\SupplierPayment;

use App\Models\SupplierPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ISupplierPaymentRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getById(int $userId, int $id): ?SupplierPayment;
    public function create(int $userId, array $data): SupplierPayment;
    public function update(int $userId, int $id, array $data): bool;
    public function delete(int $userId, int $id): bool;
}
