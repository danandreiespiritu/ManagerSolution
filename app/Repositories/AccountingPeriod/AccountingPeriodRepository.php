<?php
namespace App\Repositories\AccountingPeriod;

use App\Models\AccountingPeriod as AccountingPeriodModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AccountingPeriodRepository implements IAccountingPeriodRepository
{
    public function create(array $payload): AccountingPeriodModel
    {
        return AccountingPeriodModel::create($payload);
    }

    public function getById(int $id): ?AccountingPeriodModel
    {
        return AccountingPeriodModel::find($id);
    }

    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return AccountingPeriodModel::where('user_id', $userId)->orderByDesc('start_date')->paginate($perPage);
    }

    public function getAllForUser(int $userId): Collection
    {
        return AccountingPeriodModel::where('user_id', $userId)->orderByDesc('start_date')->get();
    }

    public function update(int $id, array $payload): ?AccountingPeriodModel
    {
        /** @var AccountingPeriodModel|null $model */
        $model = AccountingPeriodModel::find($id);
        if (!$model) return null;
        $model->fill($payload);
        $model->save();
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) AccountingPeriodModel::where('id', $id)->delete();
    }
}
