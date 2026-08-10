<?php

namespace App\Repositories\Contracts;

use App\Enums\Delivery\DeliveryStatus;
use App\Models\Delivery\Delivery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DeliveryRepositoryInterface
{
    public function create(array $data): Delivery;

    public function findById(string $id): ?Delivery;

    public function findByNumber(string $deliveryNumber): ?Delivery;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): Delivery;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByDriver(string $driverId): \Illuminate\Database\Eloquent\Collection;

    public function findByStatus(DeliveryStatus $status): \Illuminate\Database\Eloquent\Collection;
}
