<?php

namespace App\Repositories\Eloquent;

use App\Models\Inventory\StockItem;
use App\Repositories\Contracts\StockItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentStockItemRepository implements StockItemRepositoryInterface
{
    public function __construct(
        protected StockItem $model,
    ) {}

    public function create(array $data): StockItem
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?StockItem
    {
        return $this->model->with(['warehouse', 'product', 'bin'])->find($id);
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function findByWarehouse(string $warehouseId): Collection
    {
        return $this->model->where('warehouse_id', $warehouseId)->get();
    }

    public function findByProduct(string $productId): Collection
    {
        return $this->model->where('product_id', $productId)->get();
    }

    public function update(string $id, array $data): StockItem
    {
        $stockItem = $this->findById($id);
        $stockItem->update($data);
        return $stockItem->fresh();
    }

    public function delete(string $id): bool
    {
        $stockItem = $this->findById($id);
        return $stockItem->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['warehouse', 'product']);

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (! empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $query->whereColumn('quantity_on_hand', '<=', 'reorder_level');
        }

        if (isset($filters['out_of_stock']) && $filters['out_of_stock']) {
            $query->where('quantity_on_hand', '<=', 0);
        }

        if (! empty($filters['min_quantity'])) {
            $query->where('quantity_on_hand', '>=', $filters['min_quantity']);
        }

        if (! empty($filters['max_quantity'])) {
            $query->where('quantity_on_hand', '<=', $filters['max_quantity']);
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByWarehouseAndProduct(string $warehouseId, string $productId): ?StockItem
    {
        return $this->model
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();
    }
}
