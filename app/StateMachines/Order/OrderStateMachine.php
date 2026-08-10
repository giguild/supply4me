<?php

declare(strict_types=1);

namespace App\StateMachines\Order;

use App\Events\OrderStateTransitioned;
use App\Models\Order;
use App\StateMachines\Order\States\CancelledState;
use App\StateMachines\Order\States\CompletedState;
use App\StateMachines\Order\States\ConfirmedState;
use App\StateMachines\Order\States\DeliveredState;
use App\StateMachines\Order\States\DraftState;
use App\StateMachines\Order\States\OnHoldState;
use App\StateMachines\Order\States\PackingState;
use App\StateMachines\Order\States\PendingState;
use App\StateMachines\Order\States\PickingState;
use App\StateMachines\Order\States\ProcessingState;
use App\StateMachines\Order\States\ReadyToShipState;
use App\StateMachines\Order\States\ShippedState;
use Illuminate\Support\Facades\Log;

class OrderStateMachine
{
    protected array $states = [];
    protected array $transitions = [];
    protected array $guards = [];
    protected Order $order;

    public function __construct()
    {
        $this->registerStates();
        $this->registerTransitions();
    }

    public static function resolve(Order $order): self
    {
        $machine = new self();
        $machine->order = $order;
        return $machine;
    }

    protected function registerStates(): void
    {
        $this->states = [
            'draft' => new DraftState(),
            'pending' => new PendingState(),
            'confirmed' => new ConfirmedState(),
            'processing' => new ProcessingState(),
            'picking' => new PickingState(),
            'packing' => new PackingState(),
            'ready_to_ship' => new ReadyToShipState(),
            'shipped' => new ShippedState(),
            'delivered' => new DeliveredState(),
            'completed' => new CompletedState(),
            'on_hold' => new OnHoldState(),
            'cancelled' => new CancelledState(),
        ];
    }

    protected function registerTransitions(): void
    {
        $this->transitions = [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['confirmed', 'cancelled', 'on_hold'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['picking', 'cancelled', 'on_hold'],
            'picking' => ['packing'],
            'packing' => ['ready_to_ship'],
            'ready_to_ship' => ['shipped'],
            'shipped' => ['delivered'],
            'delivered' => ['completed'],
            'completed' => [],
            'on_hold' => ['processing', 'cancelled'],
            'cancelled' => [],
        ];
    }

    public function canTransition(string $toState): bool
    {
        $currentState = $this->getCurrentState();

        if (!isset($this->transitions[$currentState])) {
            return false;
        }

        $allowedTransitions = $this->transitions[$currentState];

        if (!in_array($toState, $allowedTransitions)) {
            return false;
        }

        if (isset($this->guards[$currentState][$toState])) {
            foreach ($this->guards[$currentState][$toState] as $guard) {
                if (!$guard($this->order)) {
                    return false;
                }
            }
        }

        $state = $this->getState($currentState);
        return $state->canTransitionTo($toState);
    }

    public function transition(string $toState): void
    {
        if (!$this->canTransition($toState)) {
            $currentState = $this->getCurrentState();
            throw new \DomainException(
                "Cannot transition from [{$currentState}] to [{$toState}]"
            );
        }

        $currentState = $this->getCurrentState();
        $fromState = $this->getState($currentState);
        $toStateObj = $this->getState($toState);

        $fromState->onExit($this->order, $toState);
        $toStateObj->onEnter($this->order, $currentState);

        Log::info('Order state transitioned', [
            'order_id' => $this->order->id,
            'from' => $currentState,
            'to' => $toState,
            'user_id' => auth()->id(),
        ]);

        event(new OrderStateTransitioned($this->order, $currentState, $toState));
    }

    public function addGuard(string $fromState, string $toState, callable $guard): void
    {
        $this->guards[$fromState][$toState][] = $guard;
    }

    public function getState(string $stateName): OrderState
    {
        if (!isset($this->states[$stateName])) {
            throw new \InvalidArgumentException("State [{$stateName}] does not exist.");
        }

        return $this->states[$stateName];
    }

    public function getCurrentState(): string
    {
        return $this->order->status ?? 'draft';
    }

    public function getAvailableTransitions(): array
    {
        $currentState = $this->getCurrentState();
        $available = [];

        if (isset($this->transitions[$currentState])) {
            foreach ($this->transitions[$currentState] as $toState) {
                if ($this->canTransition($toState)) {
                    $available[] = $toState;
                }
            }
        }

        return $available;
    }

    public function getAllStates(): array
    {
        return array_keys($this->states);
    }

    public function getTransitionHistory(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->order->statusHistory()->orderByDesc('transitioned_at')->get();
    }

    public function is(string $stateName): bool
    {
        return $this->getCurrentState() === $stateName;
    }

    public function isTerminal(): bool
    {
        $terminalStates = ['completed', 'cancelled'];
        return in_array($this->getCurrentState(), $terminalStates);
    }
}
