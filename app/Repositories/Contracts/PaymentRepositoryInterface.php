<?php

namespace App\Repositories\Contracts;

use App\Enums\Payments\PaymentStatus;
use App\Models\Payments\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;

    public function findById(string $id): ?Payment;

    public function findByNumber(string $paymentNumber): ?Payment;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): Payment;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByCustomer(string $customerId): \Illuminate\Database\Eloquent\Collection;

    public function findByStatus(PaymentStatus $status): \Illuminate\Database\Eloquent\Collection;
}
