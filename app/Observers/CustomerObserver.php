<?php

namespace App\Observers;

use App\Enums\Customers\CreditStatus;
use App\Events\Customers\CreditStatusChanged;
use App\Events\Customers\CustomerCreated;
use App\Events\Customers\CustomerUpdated;
use App\Models\Customers\Customer;
use Spatie\Activitylog\Facades\ActivityLog;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        ActivityLog::event('Customer created')
            ->on($customer)
            ->withProperties([
                'customer_id' => $customer->id,
                'customer_number' => $customer->customer_number,
                'name' => $customer->name,
                'company_id' => $customer->company_id,
            ])
            ->log();

        CustomerCreated::dispatch($customer);
    }

    public function updated(Customer $customer): void
    {
        $changes = $customer->getChanges();

        ActivityLog::event('Customer updated')
            ->on($customer)
            ->withProperties([
                'customer_id' => $customer->id,
                'attributes' => $changes,
                'old' => $customer->getOriginal(),
            ])
            ->log();

        if (isset($changes['credit_status'])) {
            $oldStatus = CreditStatus::tryFrom($customer->getOriginal('credit_status'));
            $newStatus = CreditStatus::tryFrom($changes['credit_status']);

            $this->validateCreditStatusTransition($oldStatus, $newStatus, $customer);

            CreditStatusChanged::dispatch($customer, $oldStatus, $newStatus);
        }

        CustomerUpdated::dispatch($customer);
    }

    public function deleted(Customer $customer): void
    {
        ActivityLog::event('Customer deleted')
            ->on($customer)
            ->withProperties([
                'customer_id' => $customer->id,
                'customer_number' => $customer->customer_number,
                'name' => $customer->name,
            ])
            ->log();
    }

    public function restored(Customer $customer): void
    {
        ActivityLog::event('Customer restored')
            ->on($customer)
            ->withProperties([
                'customer_id' => $customer->id,
            ])
            ->log();
    }

    protected function validateCreditStatusTransition(
        ?CreditStatus $oldStatus,
        ?CreditStatus $newStatus,
        Customer $customer
    ): void {
        if ($oldStatus === null || $newStatus === null) {
            return;
        }

        $validTransitions = [
            CreditStatus::Good => [CreditStatus::Overdue],
            CreditStatus::Overdue => [CreditStatus::Good, CreditStatus::Blocked],
            CreditStatus::Blocked => [CreditStatus::Good, CreditStatus::Overdue],
        ];

        if (! in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            ActivityLog::event('Invalid credit status transition attempted')
                ->on($customer)
                ->withProperties([
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'customer_id' => $customer->id,
                ])
                ->warning();
        }
    }
}
