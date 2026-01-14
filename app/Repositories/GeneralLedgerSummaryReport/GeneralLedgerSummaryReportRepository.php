<?php

namespace App\Repositories\GeneralLedgerSummaryReport;

use App\Models\GeneralLedgerSummaryReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GeneralLedgerSummaryReportRepository implements IGeneralLedgerSummaryReportRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return GeneralLedgerSummaryReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getAll(int $userId): Collection
    {
        return GeneralLedgerSummaryReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $userId, int $id): ?GeneralLedgerSummaryReport
    {
        return GeneralLedgerSummaryReport::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function create(int $userId, array $data): GeneralLedgerSummaryReport
    {
        $payload = [
            'user_id' => $userId,
            'business_id' => $data['business_id'] ?? null,
            'description' => $data['description'] ?? null,
            'from_date' => $data['from_date'] ?? null,
            'to_date' => $data['to_date'] ?? null,
            'show_codes' => $data['show_codes'] ?? false,
            'exclude_zero' => $data['exclude_zero'] ?? false,
        ];

        return GeneralLedgerSummaryReport::create($payload);
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $report = $this->getById($userId, $id);
        if (! $report) {
            return false;
        }

        unset($data['user_id'], $data['business_id']);

        return $report->update($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $report = $this->getById($userId, $id);
        if (! $report) {
            return false;
        }

        return (bool) $report->delete();
    }
}
