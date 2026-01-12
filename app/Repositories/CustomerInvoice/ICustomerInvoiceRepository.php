<?php
namespace App\Repositories\CustomerInvoice;

use App\Models\CustomerInvoice as CustomerInvoiceModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ICustomerInvoiceRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function create(int $userId, array $data): CustomerInvoiceModel;

    public function getAll(int $userId, ?int $businessId = null): Collection;

    public function getById(int $userId, int $id): ?CustomerInvoiceModel;

    public function getByCustomer(int $userId, int $customerId): Collection;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
