<?php

use App\Actions\Payments\ApprovePaymentAction;
use App\Actions\Payments\RejectPaymentAction;
use App\Enums\Payments\PaymentStatus;
use App\Events\Payments\PaymentApproved;
use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Payments\Payment;

it('approves a pending payment', function () {
    $user = User::factory()->create();
    $approver = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);

    $payment = Payment::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'status' => 'pending',
        'amount' => 500,
    ]);

    $action = app(ApprovePaymentAction::class);
    $result = $action->execute($payment, $approver);

    expect($result->status)->toBe(PaymentStatus::Completed)
        ->and($result->approved_by)->toBe($approver->id)
        ->and($result->cleared_date)->not->toBeNull();
});

it('fires PaymentApproved event', function () {
    $user = User::factory()->create();
    $approver = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);

    $payment = Payment::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'status' => 'pending',
    ]);

    Event::fake([PaymentApproved::class]);

    $action = app(ApprovePaymentAction::class);
    $action->execute($payment, $approver);

    Event::assertDispatched(PaymentApproved::class, function ($event) use ($payment) {
        return $event->payment->id === $payment->id;
    });
});

it('throws exception when approving non-pending payment', function () {
    $user = User::factory()->create();
    $approver = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);

    $payment = Payment::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'status' => 'completed',
    ]);

    $action = app(ApprovePaymentAction::class);

    $this->expectException(\App\Exceptions\PaymentCannotBeApprovedException::class);

    $action->execute($payment, $approver);
});

it('rejects a pending payment', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);

    $payment = Payment::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'status' => 'pending',
    ]);

    $action = app(RejectPaymentAction::class);
    $result = $action->execute($payment, 'Invalid payment method');

    expect($result->status)->toBe(PaymentStatus::Cancelled);
});

it('does not allow approving a refunded payment', function () {
    $user = User::factory()->create();
    $approver = User::factory()->create();
    $customer = Customer::factory()->create(['company_id' => $user->company_id]);

    $payment = Payment::factory()->create([
        'company_id' => $user->company_id,
        'customer_id' => $customer->id,
        'status' => 'refunded',
    ]);

    $action = app(ApprovePaymentAction::class);

    $this->expectException(\App\Exceptions\PaymentCannotBeApprovedException::class);

    $action->execute($payment, $approver);
});
