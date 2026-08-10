<?php

namespace App\Repositories\Contracts;

use App\Models\Customers\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function create(array $data): Customer;

    public function findById(string $id): ?Customer;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function findByNumber(string $customerNumber): ?Customer;

    public function update(string $id, array $data): Customer;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByAssignedTo(string $userId): \Illuminate\Database\Eloquent\Collection;
}
