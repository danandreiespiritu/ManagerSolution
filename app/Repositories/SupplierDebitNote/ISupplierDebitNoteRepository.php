<?php

namespace App\Repositories\SupplierDebitNote;

use App\Models\SupplierDebitNote;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ISupplierDebitNoteRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getById(int $userId, int $id): ?SupplierDebitNote;

    /**
     * Create a supplier debit note without emitting model events (prevents duplicate auto-journals).
     */
    public function createQuietly(int $userId, array $data): SupplierDebitNote;
    public function update(int $userId, int $id, array $data): bool;
    public function delete(int $userId, int $id): bool;
}
