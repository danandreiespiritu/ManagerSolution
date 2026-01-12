<?php

namespace App\Repositories\SupplierDebitNote;

use App\Models\SupplierDebitNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierDebitNoteRepository implements ISupplierDebitNoteRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return SupplierDebitNote::where('user_id', $userId)
            ->with('supplier')
            ->orderByDesc('debit_date')
            ->paginate($perPage);
    }

    public function getById(int $userId, int $id): ?SupplierDebitNote
    {
        return SupplierDebitNote::where('user_id', $userId)
            ->with('supplier')
            ->where('id', $id)
            ->first();
    }

    public function createQuietly(int $userId, array $data): SupplierDebitNote
    {
        return SupplierDebitNote::withoutEvents(function () use ($userId, $data) {
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

            return SupplierDebitNote::create($payload);
        });
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $n = SupplierDebitNote::where('user_id', $userId)->where('id', $id)->first();
        if (! $n) return false;
        $n->fill($data);
        return $n->save();
    }

    public function delete(int $userId, int $id): bool
    {
        $n = SupplierDebitNote::where('user_id', $userId)->where('id', $id)->first();
        if (! $n) return false;
        return (bool) $n->delete();
    }
}
