<?php

namespace App\Repositories\Eloquent;

use App\Enums\Invoicing\InvoiceStatus;
use App\Models\Invoicing\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        protected Invoice $model,
    ) {}

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Invoice
    {
        return $this->model->with(['items', 'customer', 'order', 'createdBy'])->find($id);
    }

    public function findByNumber(string $invoiceNumber): ?Invoice
    {
        return $this->model->where('invoice_number', $invoiceNumber)->first();
    }

    public function findByCompany(string $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function update(string $id, array $data): Invoice
    {
        $invoice = $this->findById($id);
        $invoice->update($data);
        return $invoice->fresh();
    }

    public function delete(string $id): bool
    {
        $invoice = $this->findById($id);
        return $invoice->delete();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['customer', 'order', 'createdBy']);

        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (! empty($filters['invoice_number'])) {
            $query->where('invoice_number', 'like', "%{$filters['invoice_number']}%");
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['due_date_from'])) {
            $query->where('due_date', '>=', $filters['due_date_from']);
        }

        if (! empty($filters['due_date_to'])) {
            $query->where('due_date', '<=', $filters['due_date_to']);
        }

        if (! empty($filters['min_amount'])) {
            $query->where('total_amount', '>=', $filters['min_amount']);
        }

        if (! empty($filters['max_amount'])) {
            $query->where('total_amount', '<=', $filters['max_amount']);
        }

        if (isset($filters['overdue_only']) && $filters['overdue_only']) {
            $query->where('status', '!=', InvoiceStatus::Paid)
                ->where('due_date', '<', now());
        }

        $query->latest();

        return $query->paginate($perPage);
    }

    public function findByCustomer(string $customerId): Collection
    {
        return $this->model->where('customer_id', $customerId)->latest()->get();
    }

    public function findByStatus(InvoiceStatus $status): Collection
    {
        return $this->model->where('status', $status)->latest()->get();
    }
}
