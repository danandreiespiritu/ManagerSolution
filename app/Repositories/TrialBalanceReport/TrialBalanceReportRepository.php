<?php

namespace App\Repositories\TrialBalanceReport;

use App\Models\TrialBalanceReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TrialBalanceReportRepository implements ITrialBalanceReportRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return TrialBalanceReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getAll(int $userId): Collection
    {
        return TrialBalanceReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $userId, int $id): ?TrialBalanceReport
    {
        return TrialBalanceReport::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function create(int $userId, array $data): TrialBalanceReport
    {
        $payload = [
            'user_id' => $userId,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'accounting_method' => $data['accounting_method'] ?? 'accrual',
            'footer' => $data['footer'] ?? null,
        ];

        return TrialBalanceReport::create($payload);
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
