<?php

namespace App\Repositories\Contracts;

use App\Models\Inventory\StockItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StockItemRepositoryInterface
{
    public function create(array $data): StockItem;

    public function findById(string $id): ?StockItem;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function findByWarehouse(string $warehouseId): \Illuminate\Database\Eloquent\Collection;

    public function findByProduct(string $productId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): StockItem;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByWarehouseAndProduct(string $warehouseId, string $productId): ?StockItem;
}
