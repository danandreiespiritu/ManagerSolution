<?php

namespace App\Repositories\Supplier;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository implements ISupplierRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Supplier::where('user_id', $userId)
            ->orderBy('supplier_name')
            ->paginate($perPage);
    }

    public function getById(int $userId, int $id): ?Supplier
    {
        return Supplier::where('user_id', $userId)->where('id', $id)->first();
    }

    public function create(int $userId, array $data): Supplier
    {
        return Supplier::create(array_merge($data, ['user_id' => $userId]));
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $supplier = $this->getById($userId, $id);
        if (! $supplier) return false;
        return $supplier->update($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $supplier = $this->getById($userId, $id);
        if (! $supplier) return false;
        return (bool) $supplier->delete();
    }
}
