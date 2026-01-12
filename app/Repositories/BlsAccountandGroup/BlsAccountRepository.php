<?php
namespace App\Repositories\BlsAccountandGroup;

use App\Models\ChartofAccounts as AccountModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlsAccountRepository implements IBlsAccountRepository
{
    public function paginate(int $userId, int $perPage = 15, ?int $businessId = null): LengthAwarePaginator
    {
        $query = AccountModel::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->orderBy('account_name');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->paginate($perPage);
    }
    public function create(int $userId, array $data, ?int $businessId = null): AccountModel
    {
        $data['user_id'] = $userId;
        // Map legacy input keys to unified chart_of_accounts columns
        $payload = [
            'user_id' => $userId,
            'account_type' => 'BL',
            'account_name' => $data['BlAccountName'] ?? $data['name'] ?? null,
            'account_code' => $data['BlAccountCode'] ?? $data['code'] ?? null,
            'account_group' => $data['BlAccountGroup'] ?? $data['group'] ?? null,
            'cash_flow_category' => $data['CashFlowCategory'] ?? $data['cash_flow_category'] ?? null,
        ];

        if ($businessId) {
            $payload['business_id'] = $businessId;
        }

        return AccountModel::create($payload);
    }

    public function getAll(int $userId, ?int $businessId = null): Collection
    {
        $query = AccountModel::where('user_id', $userId)
            ->where('account_type', 'BL')
            ->orderBy('account_name');

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->get();
    }

    public function getById(int $userId, int $id, ?int $businessId = null): ?AccountModel
    {
        $query = AccountModel::where('user_id', $userId)
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
}
