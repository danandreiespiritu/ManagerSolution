<?php
namespace App\Repositories\BlsAccountandGroup;

use App\Models\ChartofAccounts as BlGroupModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlsGroupRepository implements IBlsGroupRepository
{
    public function paginate(int $userId, int $perPage = 15, ?int $businessId = null): LengthAwarePaginator
    {
        $query = BlGroupModel::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->orderBy('group');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->paginate($perPage);
    }

    public function create(int $userId, array $data, ?int $businessId = null): BlGroupModel
    {
        $data['user_id'] = $userId;
        $payload = [
            'user_id' => $userId,
            'account_type' => 'BL',
            'group' => $data['BlName'] ?? $data['name'] ?? null,
            'group_category' => $data['BlCategory'] ?? $data['category'] ?? null,
        ];

        if ($businessId) {
            $payload['business_id'] = $businessId;
        }

        return BlGroupModel::create($payload);
    }

    public function getAll(int $userId, ?int $businessId = null): Collection
    {
        $query = BlGroupModel::where('user_id', $userId)
            ->where('group', 'BL')
            ->orderBy('group');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->get();
    }

    public function getById(int $userId, int $id, ?int $businessId = null): ?BlGroupModel
    {
        $query = BlGroupModel::where('user_id', $userId)
            ->where('id', $id)
            ->where('account_type', 'BL');

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

    public function getCategory(int $id, int $userId, ?int $businessId = null): ?BlGroupModel
    {
        return $this->getById($userId, $id, $businessId);
    }
}
