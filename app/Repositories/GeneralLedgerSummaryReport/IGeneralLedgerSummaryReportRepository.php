<?php

namespace App\Repositories\GeneralLedgerSummaryReport;

use App\Models\GeneralLedgerSummaryReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface IGeneralLedgerSummaryReportRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAll(int $userId): Collection;

    public function getById(int $userId, int $id): ?GeneralLedgerSummaryReport;

    public function create(int $userId, array $data): GeneralLedgerSummaryReport;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
