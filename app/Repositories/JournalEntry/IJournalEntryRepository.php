<?php
namespace App\Repositories\JournalEntry;

use App\Models\JournalEntry as JournalEntryModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IJournalEntryRepository
{
    public function create(array $payload): JournalEntryModel;

    public function getById(int $id): ?JournalEntryModel;

    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAllForUser(int $userId): Collection;

    public function update(int $id, array $payload): JournalEntryModel;

    public function delete(int $id): bool;
}
