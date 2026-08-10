<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Payments\Payment;
use App\StateMachines\Payment\PaymentStateMachine;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
});

it('resolves the state machine for a payment', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine)->toBeInstanceOf(PaymentStateMachine::class)
        ->and($machine->getCurrentState())->toBe('pending');
});

it('can transition from pending to processing', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('processing'))->toBeTrue();

    $machine->transition('processing');

    expect($machine->getCurrentState())->toBe('processing');
});

it('can transition from processing to completed', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'processing',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('completed'))->toBeTrue();

    $machine->transition('completed');

    expect($machine->getCurrentState())->toBe('completed');
});

it('can transition from processing to failed', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'processing',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('failed'))->toBeTrue();

    $machine->transition('failed');

    expect($machine->getCurrentState())->toBe('failed');
});

it('can transition from completed to refunded', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('refunded'))->toBeTrue();

    $machine->transition('refunded');

    expect($machine->getCurrentState())->toBe('refunded');
});

it('can cancel from pending', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('cancelled'))->toBeTrue();

    $machine->transition('cancelled');

    expect($machine->getCurrentState())->toBe('cancelled');
});

it('cannot transition from completed to pending', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('pending'))->toBeFalse();
});

it('cannot transition from refunded to any state', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'refunded',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('completed'))->toBeFalse()
        ->and($machine->canTransition('cancelled'))->toBeFalse();
});

it('cannot transition from cancelled to any state', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'cancelled',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->canTransition('pending'))->toBeFalse()
        ->and($machine->canTransition('completed'))->toBeFalse();
});

it('throws exception for invalid transition', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    $this->expectException(\DomainException::class);

    $machine->transition('refunded');
});

it('gets available transitions', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    $transitions = $machine->getAvailableTransitions();

    expect($transitions)->toContain('processing')
        ->and($transitions)->toContain('cancelled');
});

it('identifies terminal states', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'refunded',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->isTerminal())->toBeTrue();
});

it('identifies non-terminal states', function () {
    $payment = Payment::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'processing',
    ]);

    $machine = PaymentStateMachine::resolve($payment);

    expect($machine->isTerminal())->toBeFalse();
});
