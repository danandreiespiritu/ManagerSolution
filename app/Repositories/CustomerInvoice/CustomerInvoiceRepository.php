<?php
namespace App\Repositories\CustomerInvoice;

use App\Models\CustomerInvoice as CustomerInvoiceModel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerInvoiceRepository implements ICustomerInvoiceRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return CustomerInvoiceModel::where('user_id', $userId)
            ->orderByDesc('invoice_date')
            ->paginate($perPage);
    }

    public function create(int $userId, array $data): CustomerInvoiceModel
    {
        $payload = array_merge($data, ['user_id' => $userId]);

        return CustomerInvoiceModel::create($payload);
    }

    public function getAll(int $userId, ?int $businessId = null): Collection
    {
        $query = CustomerInvoiceModel::where('user_id', $userId);

        if ($businessId !== null) {
            $query->where('business_id', $businessId);
        }

        return $query->orderByDesc('invoice_date')->get();
    }

    public function getById(int $userId, int $id): ?CustomerInvoiceModel
    {
        return CustomerInvoiceModel::where('user_id', $userId)
            ->where('id', $id)
            ->first();
    }

    public function getByCustomer(int $userId, int $customerId): Collection
    {
        return CustomerInvoiceModel::where('user_id', $userId)
            ->where('customer_id', $customerId)
            ->orderByDesc('invoice_date')
            ->get();
    }

    public function update(int $userId, int $id, array $data): bool
    {
        $inv = $this->getById($userId, $id);

        if (! $inv) {
            return false;
        }

        return $inv->update($data);
    }

    public function delete(int $userId, int $id): bool
    {
        $inv = $this->getById($userId, $id);

        if (! $inv) {
            return false;
        }

        return $inv->delete();
    }
}
