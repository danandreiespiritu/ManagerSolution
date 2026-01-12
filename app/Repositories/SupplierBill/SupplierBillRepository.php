<?php

namespace App\Repositories\SupplierBill;

use App\Models\SupplierBill;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierBillRepository implements ISupplierBillRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return SupplierBill::where('user_id', $userId)
            ->with('supplier')
            ->orderByDesc('bill_date')
            ->paginate($perPage);
    }

    public function getById(int $userId, int $id): ?SupplierBill
    {
        return SupplierBill::where('user_id', $userId)
            ->with(['supplier','billPayments'])
            ->where('id', $id)
            ->first();
    }

    public function createQuietly(int $userId, array $data): SupplierBill
    {
        return SupplierBill::withoutEvents(function () use ($userId, $data) {
            $businessId = null;
            if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
                $businessId = $b->id ?? null;
            } elseif (session()->has('current_business_id')) {
                $businessId = (int) session('current_business_id');
            }

            $payload = array_merge($data, ['user_id' => $userId]);
            if ($businessId && empty($payload['business_id'])) {
                $payload['business_id'] = $businessId;
            }

            return SupplierBill::create($payload);
        });
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $bill = SupplierBill::where('user_id', $userId)->where('id', $id)->first();
        if (! $bill) return false;
        return $bill->update($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $bill = SupplierBill::where('user_id', $userId)->where('id', $id)->first();
        if (! $bill) return false;
        return (bool) $bill->delete();
    }
}
