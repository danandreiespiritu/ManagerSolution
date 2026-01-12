<?php

namespace App\Repositories\CustomerCreditNote;

use App\Models\CustomerCreditNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ICustomerCreditNoteRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getById(int $userId, int $id): ?CustomerCreditNote;

    public function create(int $userId, array $data): CustomerCreditNote;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
