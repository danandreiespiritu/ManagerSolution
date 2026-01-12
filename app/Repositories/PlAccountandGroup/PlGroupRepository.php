<?php
namespace App\Repositories\PlAccountandGroup;

use App\Models\ChartofAccounts as PlGroupModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlGroupRepository implements IPlGroupRepository
{
    public function paginate(int $userId, int $perPage = 15, ?int $businessId = null): LengthAwarePaginator
    {
        $query = PlGroupModel::where('user_id', $userId)
            ->where('account_type', 'PL')
            ->orderBy('group');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->paginate($perPage);
    }

    public function create(int $userId, array $data, ?int $businessId = null): PlGroupModel
    {
        $data['user_id'] = $userId;
        $payload = [
            'user_id' => $userId,
            'account_type' => 'PL',
            'group' => $data['PlName'] ?? $data['name'] ?? null,
            'group_category' => $data['PlCategory'] ?? $data['category'] ?? null,
        ];

        if ($businessId) {
            $payload['business_id'] = $businessId;
        }

        return PlGroupModel::create($payload);
    }

    public function getAll(int $userId, ?int $businessId = null): Collection
    {
        $query = PlGroupModel::where('user_id', $userId)
            ->where('group', 'PL')
            ->orderBy('group');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->get();
    }

    public function getById(int $userId, int $id, ?int $businessId = null): ?PlGroupModel
    {
        $query = PlGroupModel::where('user_id', $userId)
            ->where('id', $id)
            ->where('account_type', 'PL');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->first();
    }

    public function update(int $userId, int $id, array $data, ?int $businessId = null): bool
    {
        $account = $this->getById($userId, $id, $businessId);

        if (! $account) {
            return false;
        }

        unset($data['business_id']);

        return $account->update($data);
    }

    public function delete(int $userId, int $id, ?int $businessId = null): bool
    {
        $account = $this->getById($userId, $id, $businessId);

        if (! $account) {
            return false;
        }

        return $account->delete();
    }

    public function getCategory(int $id, int $userId, ?int $businessId = null): ?PlGroupModel
    {
        return $this->getById($userId, $id, $businessId);
    }
}
