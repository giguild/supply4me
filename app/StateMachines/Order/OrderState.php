<?php

declare(strict_types=1);

namespace App\StateMachines\Order;

use App\Contracts\StateInterface;
use App\Models\Order;

abstract class OrderState implements StateInterface
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

    public function onEnter(Order $order, string $previousState): void
    {
        $order->status = $this->name;
        $order->save();

        $this->recordHistory($order, $previousState, $this->name);
    }

    public function onExit(Order $order, string $nextState): void
    {
        // Hook for subclasses
    }

    protected function recordHistory(Order $order, string $from, string $to): void
    {
        $order->statusHistory()->create([
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
