<?php

namespace App\Repositories\Contracts;

use App\Enums\Invoicing\InvoiceStatus;
use App\Models\Invoicing\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    public function create(array $data): Invoice;

    public function findById(string $id): ?Invoice;

    public function findByNumber(string $invoiceNumber): ?Invoice;

    public function findByCompany(string $companyId): \Illuminate\Database\Eloquent\Collection;

    public function update(string $id, array $data): Invoice;

    public function delete(string $id): bool;

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByCustomer(string $customerId): \Illuminate\Database\Eloquent\Collection;

    public function findByStatus(InvoiceStatus $status): \Illuminate\Database\Eloquent\Collection;
}
