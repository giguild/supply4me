<?php

declare(strict_types=1);

namespace App\StateMachines\Payment;

use App\Contracts\StateInterface;
use App\Models\Payment;

abstract class PaymentState implements StateInterface
{
    protected string $name;
    protected string $label;
    protected string $color;

    public function __construct()
    {
        $this->name = class_basename(static::class);
        $this->label = str_replace('State', '', $this->name);
        $this->color = '#6b7280';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function color(): string
    {
        return $this->color;
    }

    abstract public function canTransitionTo(string $state): bool;

    public function onEnter(Payment $payment, string $previousState): void
    {
        $payment->status = $this->name;
        $payment->save();

        $this->recordHistory($payment, $previousState, $this->name);
    }

    public function onExit(Payment $payment, string $nextState): void
    {
        // Hook for subclasses
    }

    protected function recordHistory(Payment $payment, string $from, string $to): void
    {
        $payment->statusHistory()->create([
            'from_state' => $from,
            'to_state' => $to,
            'transitioned_at' => now(),
            'metadata' => json_encode([
                'user_id' => auth()->id(),
            ]),
        ]);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
