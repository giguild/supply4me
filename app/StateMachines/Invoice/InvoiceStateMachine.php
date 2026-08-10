<?php

declare(strict_types=1);

namespace App\StateMachines\Invoice;

use App\Events\InvoiceStateTransitioned;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class InvoiceStateMachine
{
    protected array $states = [];
    protected array $transitions = [];
    protected array $guards = [];
    protected Invoice $invoice;

    public function __construct()
    {
        $this->registerStates();
        $this->registerTransitions();
    }

    public static function resolve(Invoice $invoice): self
    {
        $machine = new self();
        $machine->invoice = $invoice;
        return $machine;
    }

    protected function registerStates(): void
    {
        $this->states = [
            'draft' => new States\DraftState(),
            'pending' => new States\PendingState(),
            'sent' => new States\SentState(),
            'viewed' => new States\ViewedState(),
            'paid' => new States\PaidState(),
            'partial' => new States\PartialState(),
            'overdue' => new States\OverdueState(),
            'cancelled' => new States\CancelledState(),
            'void' => new States\VoidState(),
        ];
    }

    protected function registerTransitions(): void
    {
        $this->transitions = [
            'draft' => ['pending', 'cancelled'],
            'pending' => ['sent', 'cancelled'],
            'sent' => ['viewed', 'overdue'],
            'viewed' => ['paid', 'partial', 'overdue'],
            'partial' => ['paid', 'overdue'],
            'overdue' => ['paid', 'cancelled'],
            'paid' => [],
            'cancelled' => [],
            'void' => [],
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
                if (!$guard($this->invoice)) {
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
                "Cannot transition invoice from [{$currentState}] to [{$toState}]"
            );
        }

        $currentState = $this->getCurrentState();
        $fromState = $this->getState($currentState);
        $toStateObj = $this->getState($toState);

        $fromState->onExit($this->invoice, $toState);
        $toStateObj->onEnter($this->invoice, $currentState);

        Log::info('Invoice state transitioned', [
            'invoice_id' => $this->invoice->id,
            'from' => $currentState,
            'to' => $toState,
            'user_id' => auth()->id(),
        ]);

        event(new InvoiceStateTransitioned($this->invoice, $currentState, $toState));
    }

    public function addGuard(string $fromState, string $toState, callable $guard): void
    {
        $this->guards[$fromState][$toState][] = $guard;
    }

    public function getState(string $stateName): InvoiceState
    {
        if (!isset($this->states[$stateName])) {
            throw new \InvalidArgumentException("State [{$stateName}] does not exist.");
        }

        return $this->states[$stateName];
    }

    public function getCurrentState(): string
    {
        return $this->invoice->status ?? 'draft';
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
        return $this->invoice->statusHistory()->orderByDesc('transitioned_at')->get();
    }

    public function is(string $stateName): bool
    {
        return $this->getCurrentState() === $stateName;
    }

    public function isTerminal(): bool
    {
        $terminalStates = ['paid', 'cancelled', 'void'];
        return in_array($this->getCurrentState(), $terminalStates);
    }
}
