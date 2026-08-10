<?php

namespace App\Repositories\Contracts;

use App\Models\Suppliers\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function create(array $data): Supplier;

    public function findById(string $id): ?Supplier;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): Supplier;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
