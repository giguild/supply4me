<?php

declare(strict_types=1);

namespace App\Contracts;

interface StateInterface
{
    public function name(): string;

    public function label(): string;

    public function color(): string;

    public function canTransitionTo(string $state): bool;
}
