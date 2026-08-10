<?php

declare(strict_types=1);

namespace App\StateMachines\Payment;

use App\Events\PaymentStateTransitioned;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentStateMachine
{
    protected array $states = [];
    protected array $transitions = [];
    protected array $guards = [];
    protected Payment $payment;

    public function __construct()
    {
        $this->registerStates();
        $this->registerTransitions();
    }

    public static function resolve(Payment $payment): self
    {
        $machine = new self();
        $machine->payment = $payment;
        return $machine;
    }

    protected function registerStates(): void
    {
        $this->states = [
            'pending' => new States\PendingState(),
            'processing' => new States\ProcessingState(),
            'completed' => new States\CompletedState(),
            'failed' => new States\FailedState(),
            'cancelled' => new States\CancelledState(),
            'refunded' => new States\RefundedState(),
        ];
    }

    protected function registerTransitions(): void
    {
        $this->transitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'failed'],
            'completed' => ['refunded'],
            'failed' => ['pending', 'cancelled'],
            'cancelled' => [],
            'refunded' => [],
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
                if (!$guard($this->payment)) {
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
                "Cannot transition payment from [{$currentState}] to [{$toState}]"
            );
        }

        $currentState = $this->getCurrentState();
        $fromState = $this->getState($currentState);
        $toStateObj = $this->getState($toState);

        $fromState->onExit($this->payment, $toState);
        $toStateObj->onEnter($this->payment, $currentState);

        Log::info('Payment state transitioned', [
            'payment_id' => $this->payment->id,
            'from' => $currentState,
            'to' => $toState,
            'user_id' => auth()->id(),
        ]);

        event(new PaymentStateTransitioned($this->payment, $currentState, $toState));
    }

    public function addGuard(string $fromState, string $toState, callable $guard): void
    {
        $this->guards[$fromState][$toState][] = $guard;
    }

    public function getState(string $stateName): PaymentState
    {
        if (!isset($this->states[$stateName])) {
            throw new \InvalidArgumentException("State [{$stateName}] does not exist.");
        }

        return $this->states[$stateName];
    }

    public function getCurrentState(): string
    {
        return $this->payment->status ?? 'pending';
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
        return $this->payment->statusHistory()->orderByDesc('transitioned_at')->get();
    }

    public function is(string $stateName): bool
    {
        return $this->getCurrentState() === $stateName;
    }

    public function isTerminal(): bool
    {
        $terminalStates = ['completed', 'cancelled', 'refunded'];
        return in_array($this->getCurrentState(), $terminalStates);
    }
}
