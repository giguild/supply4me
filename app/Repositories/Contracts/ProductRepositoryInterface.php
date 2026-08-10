<?php

namespace App\Repositories\Contracts;

use App\Models\Products\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function create(array $data): Product;

    public function findById(string $id): ?Product;

    public function findBySku(string $sku): ?Product;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): Product;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByCategory(string $categoryId): \Illuminate\Database\Eloquent\Collection;
}
