<?php

declare(strict_types=1);

namespace App\StateMachines\Delivery;

use App\Contracts\StateInterface;
use App\Models\Delivery;

abstract class DeliveryState implements StateInterface
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

    public function onEnter(Delivery $delivery, string $previousState): void
    {
        $delivery->status = $this->name;
        $delivery->save();

        $this->recordHistory($delivery, $previousState, $this->name);
    }

    public function onExit(Delivery $delivery, string $nextState): void
    {
        // Hook for subclasses
    }

    protected function recordHistory(Delivery $delivery, string $from, string $to): void
    {
        $delivery->statusHistory()->create([
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
