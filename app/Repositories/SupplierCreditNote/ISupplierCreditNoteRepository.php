<?php

namespace App\Repositories\SupplierCreditNote;

use App\Models\SupplierCreditNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ISupplierCreditNoteRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getById(int $userId, int $id): ?SupplierCreditNote;
    public function create(int $userId, array $data): SupplierCreditNote;
    public function update(int $userId, int $id, array $data): bool;
    public function delete(int $userId, int $id): bool;
}
