<?php
namespace App\Repositories\Customer;

use App\Models\Customer as CustomerModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements ICustomerRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return CustomerModel::where('user_id', $userId)
            ->orderBy('customer_name')
            ->paginate($perPage);
    }

    public function create(int $userId, array $data): CustomerModel
    {
        $payload = array_merge($data, ['user_id' => $userId]);

        return CustomerModel::create($payload);
    }

    public function getAll(int $userId, ?int $businessId = null): Collection
    {
        $query = CustomerModel::where('user_id', $userId);

        if ($businessId !== null) {
            $query->where('business_id', $businessId);
        }

        return $query->orderBy('customer_name')->get();
    }

    public function getById(int $userId, int $id): ?CustomerModel
    {
        return CustomerModel::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $cust = $this->getById($userId, $id);

        if (! $cust) {
            return false;
        }

        return $cust->update($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $cust = $this->getById($userId, $id);

        if (! $cust) {
            return false;
        }

        return $cust->delete();
    }
}
