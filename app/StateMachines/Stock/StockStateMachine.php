<?php

declare(strict_types=1);

namespace App\StateMachines\Stock;

use App\Events\StockStateTransitioned;
use App\Models\StockReservation;
use Illuminate\Support\Facades\Log;

class StockStateMachine
{
    protected array $states = [];
    protected array $transitions = [];
    protected array $guards = [];
    protected StockReservation $stock;

    public function __construct()
    {
        $this->registerStates();
        $this->registerTransitions();
    }

    public static function resolve(StockReservation $stock): self
    {
        $machine = new self();
        $machine->stock = $stock;
        return $machine;
    }

    protected function registerStates(): void
    {
        $this->states = [
            'available' => new States\AvailableState(),
            'reserved' => new States\ReservedState(),
            'allocated' => new States\AllocatedState(),
            'released' => new States\ReleasedState(),
            'shipped' => new States\ShippedState(),
        ];
    }

    protected function registerTransitions(): void
    {
        $this->transitions = [
            'available' => ['reserved'],
            'reserved' => ['allocated', 'released'],
            'allocated' => ['shipped'],
            'released' => ['available'],
            'shipped' => [],
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
                if (!$guard($this->stock)) {
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
                "Cannot transition stock from [{$currentState}] to [{$toState}]"
            );
        }

        $currentState = $this->getCurrentState();
        $fromState = $this->getState($currentState);
        $toStateObj = $this->getState($toState);

        $fromState->onExit($this->stock, $toState);
        $toStateObj->onEnter($this->stock, $currentState);

        Log::info('Stock state transitioned', [
            'stock_id' => $this->stock->id,
            'from' => $currentState,
            'to' => $toState,
            'user_id' => auth()->id(),
        ]);

        event(new StockStateTransitioned($this->stock, $currentState, $toState));
    }

    public function addGuard(string $fromState, string $toState, callable $guard): void
    {
        $this->guards[$fromState][$toState][] = $guard;
    }

    public function getState(string $stateName): StockState
    {
        if (!isset($this->states[$stateName])) {
            throw new \InvalidArgumentException("State [{$stateName}] does not exist.");
        }

        return $this->states[$stateName];
    }

    public function getCurrentState(): string
    {
        return $this->stock->status ?? 'available';
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
        return $this->stock->statusHistory()->orderByDesc('transitioned_at')->get();
    }

    public function is(string $stateName): bool
    {
        return $this->getCurrentState() === $stateName;
    }

    public function isTerminal(): bool
    {
        $terminalStates = ['shipped'];
        return in_array($this->getCurrentState(), $terminalStates);
    }
}
