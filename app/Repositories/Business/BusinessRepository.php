<?php
namespace App\Repositories\Business;

use App\Models\Business as BusinessModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BusinessRepository implements IBusinessRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return BusinessModel::where('user_id', $userId)
            ->orderBy('business_name')
            ->paginate($perPage);
    }

    public function create(int $userId, array $data): BusinessModel
    {
        $payload = array_merge($data, ['user_id' => $userId]);

        return BusinessModel::create($payload);
    }

    public function getAll(int $userId): Collection
    {
        return BusinessModel::where('user_id', $userId)
            ->orderBy('business_name')
            ->get();
    }

    public function getById(int $userId, int $id): ?BusinessModel
    {
        return BusinessModel::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $biz = $this->getById($userId, $id);

        if (! $biz) {
            return false;
        }

        return $biz->update($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $biz = $this->getById($userId, $id);

        if (! $biz) {
            return false;
        }

        return $biz->delete();
    }
}
