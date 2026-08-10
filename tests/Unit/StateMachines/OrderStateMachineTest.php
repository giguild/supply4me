<?php

use App\Models\Core\User;
use App\Models\Customers\Customer;
use App\Models\Orders\Order;
use App\StateMachines\Order\OrderStateMachine;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->customer = Customer::factory()->create(['company_id' => $this->user->company_id]);
});

it('resolves the state machine for an order', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine)->toBeInstanceOf(OrderStateMachine::class)
        ->and($machine->getCurrentState())->toBe('draft');
});

it('can transition from draft to pending', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('pending'))->toBeTrue();

    $machine->transition('pending');

    expect($machine->getCurrentState())->toBe('pending');
});

it('can transition from pending to confirmed', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('confirmed'))->toBeTrue();

    $machine->transition('confirmed');

    expect($machine->getCurrentState())->toBe('confirmed');
});

it('can transition from confirmed to processing', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'confirmed',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('processing'))->toBeTrue();

    $machine->transition('processing');

    expect($machine->getCurrentState())->toBe('processing');
});

it('can cancel from pending', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('cancelled'))->toBeTrue();

    $machine->transition('cancelled');

    expect($machine->getCurrentState())->toBe('cancelled');
});

it('cannot transition from draft to processing', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('processing'))->toBeFalse();
});

it('cannot transition from completed to any state', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('pending'))->toBeFalse()
        ->and($machine->canTransition('cancelled'))->toBeFalse();
});

it('cannot transition from cancelled to any state', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'cancelled',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->canTransition('pending'))->toBeFalse()
        ->and($machine->canTransition('completed'))->toBeFalse();
});

it('throws exception for invalid transition', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $machine = OrderStateMachine::resolve($order);

    $this->expectException(\DomainException::class);

    $machine->transition('processing');
});

it('gets available transitions for current state', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $machine = OrderStateMachine::resolve($order);

    $transitions = $machine->getAvailableTransitions();

    expect($transitions)->toContain('pending')
        ->and($transitions)->toContain('cancelled');
});

it('identifies terminal states', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'completed',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->isTerminal())->toBeTrue();
});

it('identifies non-terminal states', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'pending',
    ]);

    $machine = OrderStateMachine::resolve($order);

    expect($machine->isTerminal())->toBeFalse();
});

it('gets all available states', function () {
    $order = Order::factory()->create([
        'company_id' => $this->user->company_id,
        'customer_id' => $this->customer->id,
        'status' => 'draft',
    ]);

    $machine = OrderStateMachine::resolve($order);

    $allStates = $machine->getAllStates();

    expect($allStates)->toContain('draft')
        ->and($allStates)->toContain('pending')
        ->and($allStates)->toContain('confirmed')
        ->and($allStates)->toContain('cancelled')
        ->and($allStates)->toContain('completed');
});
