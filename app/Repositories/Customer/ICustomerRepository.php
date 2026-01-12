<?php
namespace App\Repositories\Customer;

use App\Models\Customer as CustomerModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ICustomerRepository
{
    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function create(int $userId, array $data): CustomerModel;

    public function getAll(int $userId, ?int $businessId = null): Collection;

    public function getById(int $userId, int $id): ?CustomerModel;

    public function update(int $userId, int $id, array $data): bool;

    public function delete(int $userId, int $id): bool;
}
