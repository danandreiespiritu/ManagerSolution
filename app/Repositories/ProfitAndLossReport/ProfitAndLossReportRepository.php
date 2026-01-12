<?php

namespace App\Repositories\ProfitAndLossReport;

use App\Models\ProfitAndLossReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfitAndLossReportRepository implements IProfitAndLossReportRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return ProfitAndLossReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getAll(int $userId): Collection
    {
        return ProfitAndLossReport::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $userId, int $id): ?ProfitAndLossReport
    {
        return ProfitAndLossReport::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function create(int $userId, array $data): ProfitAndLossReport
    {
        $payload = [
            'user_id' => $userId,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'accounting_method' => $data['accounting_method'] ?? 'accrual',
            'rounding' => $data['rounding'] ?? 'off',
            'footer' => $data['footer'] ?? null,
        ];

        return ProfitAndLossReport::create($payload);
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
