<?php

namespace App\Listeners\Payments;

use App\Events\Payments\PaymentCompleted;
use App\Models\Customers\Customer;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCustomerCredit implements ShouldQueue
{
    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment;

        if ($payment->customer_id) {
            $customer = Customer::find($payment->customer_id);

            if ($customer) {
                $totalPaid = $customer->payments()->where('status', 'completed')->sum('amount');
                $totalOwed = $customer->invoices()->whereNotIn('status', ['paid', 'voided'])->sum('balance_due');
                $availableCredit = $customer->credit_limit - ($totalOwed - $totalPaid);

                $creditStatus = match (true) {
                    $availableCredit <= 0 => \App\Enums\Customers\CreditStatus::Suspended,
                    $availableCredit <= $customer->credit_limit * 0.2 => \App\Enums\Customers\CreditStatus::Warning,
                    default => \App\Enums\Customers\CreditStatus::Active,
                };

                $customer->update(['credit_status' => $creditStatus]);
            }
        }
    }
}
