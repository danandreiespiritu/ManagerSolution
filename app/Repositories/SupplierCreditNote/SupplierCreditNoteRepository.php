<?php

namespace App\Repositories\SupplierCreditNote;

use App\Models\SupplierCreditNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierCreditNoteRepository implements ISupplierCreditNoteRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return SupplierCreditNote::where('user_id', $userId)
            ->with('supplier')
            ->orderByDesc('credit_date')
            ->paginate($perPage);
    }

    public function getById(int $userId, int $id): ?SupplierCreditNote
    {
        return SupplierCreditNote::where('user_id', $userId)
            ->with('supplier')
            ->where('id', $id)
            ->first();
    }

    public function create(int $userId, array $data): SupplierCreditNote
    {
        return SupplierCreditNote::create(array_merge($data, ['user_id' => $userId]));
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $n = SupplierCreditNote::where('user_id', $userId)->where('id', $id)->first();
        if (! $n) return false;
        $n->fill($data);
        return $n->save();
    }

    public function delete(int $userId, int $id): bool
    {
        $n = SupplierCreditNote::where('user_id', $userId)->where('id', $id)->first();
        if (! $n) return false;
        return (bool) $n->delete();
    }
}
