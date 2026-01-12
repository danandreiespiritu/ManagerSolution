<?php

namespace App\Repositories\SupplierPayment;

use App\Models\SupplierPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierPaymentRepository implements ISupplierPaymentRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return SupplierPayment::where('user_id', $userId)
            ->with(['supplier','bills'])
            ->orderByDesc('payment_date')
            ->paginate($perPage);
    }

    public function getById(int $userId, int $id): ?SupplierPayment
    {
        return SupplierPayment::where('user_id', $userId)
            ->with(['supplier','billPayments'])
            ->where('id', $id)
            ->first();
    }

    public function create(int $userId, array $data): SupplierPayment
    {
        // Ensure DB-not-nullable fields have sensible defaults when not provided from the form
        $defaults = [
            'payment_method' => '',
            'reference' => '',
        ];

        $payload = array_merge($defaults, $data, ['user_id' => $userId]);

        return SupplierPayment::create($payload);
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $p = SupplierPayment::where('user_id', $userId)->where('id', $id)->first();
        if (! $p) return false;
        $p->fill($data);
        return $p->save();
    }

    public function delete(int $userId, int $id): bool
    {
        $p = SupplierPayment::where('user_id', $userId)->where('id', $id)->first();
        if (! $p) return false;
        return (bool) $p->delete();
    }
}
