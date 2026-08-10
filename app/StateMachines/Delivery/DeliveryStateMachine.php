<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery;

use App\Events\DeliveryStateTransitioned;
use App\Models\Delivery;
use Illuminate\Support\Facades\Log;

class DeliveryStateMachine
{
    protected array $states = [];
    protected array $transitions = [];
    protected array $guards = [];
    protected Delivery $delivery;

    public function __construct()
    {
        $this->registerStates();
        $this->registerTransitions();
    }

    public static function resolve(Delivery $delivery): self
    {
        $machine = new self();
        $machine->delivery = $delivery;
        return $machine;
    }

    protected function registerStates(): void
    {
        $this->states = [
            'pending' => new States\PendingState(),
            'assigned' => new States\AssignedState(),
            'out_for_delivery' => new States\OutForDeliveryState(),
            'delivered' => new States\DeliveredState(),
            'partial_delivery' => new States\PartialDeliveryState(),
            'failed_attempt' => new States\FailedAttemptState(),
            'returned' => new States\ReturnedState(),
            'cancelled' => new States\CancelledState(),
        ];
    }

    protected function registerTransitions(): void
    {
        $this->transitions = [
            'pending' => ['assigned', 'cancelled'],
            'assigned' => ['out_for_delivery', 'cancelled'],
            'out_for_delivery' => ['delivered', 'failed_attempt', 'partial_delivery'],
            'failed_attempt' => ['assigned', 'cancelled'],
            'delivered' => ['returned'],
            'partial_delivery' => ['delivered'],
            'returned' => [],
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
                if (!$guard($this->delivery)) {
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
                "Cannot transition delivery from [{$currentState}] to [{$toState}]"
            );
        }

        $currentState = $this->getCurrentState();
        $fromState = $this->getState($currentState);
        $toStateObj = $this->getState($toState);

        $fromState->onExit($this->delivery, $toState);
        $toStateObj->onEnter($this->delivery, $currentState);

        Log::info('Delivery state transitioned', [
            'delivery_id' => $this->delivery->id,
            'from' => $currentState,
            'to' => $toState,
            'user_id' => auth()->id(),
        ]);

        event(new DeliveryStateTransitioned($this->delivery, $currentState, $toState));
    }

    public function addGuard(string $fromState, string $toState, callable $guard): void
    {
        $this->guards[$fromState][$toState][] = $guard;
    }

    public function getState(string $stateName): DeliveryState
    {
        if (!isset($this->states[$stateName])) {
            throw new \InvalidArgumentException("State [{$stateName}] does not exist.");
        }

        return $this->states[$stateName];
    }

    public function getCurrentState(): string
    {
        return $this->delivery->status ?? 'pending';
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
        return $this->delivery->statusHistory()->orderByDesc('transitioned_at')->get();
    }

    public function is(string $stateName): bool
    {
        return $this->getCurrentState() === $stateName;
    }

    public function isTerminal(): bool
    {
        $terminalStates = ['delivered', 'returned', 'cancelled'];
        return in_array($this->getCurrentState(), $terminalStates);
    }
}
