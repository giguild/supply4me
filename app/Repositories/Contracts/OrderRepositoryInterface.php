<?php

namespace App\Repositories\Contracts;

use App\Enums\Orders\OrderStatus;
use App\Models\Orders\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function findById(string $id): ?Order;

    public function findByNumber(string $orderNumber): ?Order;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): Order;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByCustomer(string $customerId): \Illuminate\Database\Eloquent\Collection;

    public function findByStatus(OrderStatus $status): \Illuminate\Database\Eloquent\Collection;
}
