<?php

namespace App\Repositories\CustomerCreditNote;

use App\Models\CustomerCreditNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerCreditNoteRepository implements ICustomerCreditNoteRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return CustomerCreditNote::where('user_id', $userId)
            ->with('customer')
            ->orderByDesc('credit_date')
            ->paginate($perPage);
    }

    public function getById(int $userId, int $id): ?CustomerCreditNote
    {
        return CustomerCreditNote::where('user_id', $userId)
            ->with('customer')
            ->where('id', $id)
            ->first();
    }

    public function create(int $userId, array $data): CustomerCreditNote
    {
        return CustomerCreditNote::create(array_merge($data, ['user_id' => $userId]));
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $n = CustomerCreditNote::where('user_id', $userId)->where('id', $id)->first();
        if (! $n) return false;
        $n->fill($data);
        return $n->save();
    }

    public function delete(int $userId, int $id): bool
    {
        $n = CustomerCreditNote::where('user_id', $userId)->where('id', $id)->first();
        if (! $n) return false;
        return (bool) $n->delete();
    }
}
