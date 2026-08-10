<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class Quantity
{
    private float $value;

    private string $unit;

    public function __construct(float $value, string $unit = 'pcs')
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative.');
        }

        $this->value = $value;
        $this->unit = strtolower($unit);
    }

    public static function from(float $value, string $unit = 'pcs'): self
    {
        return new self($value, $unit);
    }

    public static function zero(string $unit = 'pcs'): self
    {
        return new self(0, $unit);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function add(self $other): self
    {
        $this->assertSameUnit($other);

        return new self($this->value + $other->value, $this->unit);
    }

    public function subtract(self $other): self
    {
        $this->assertSameUnit($other);

        if ($this->value < $other->value) {
            throw new InvalidArgumentException('Cannot subtract a larger quantity.');
        }

        return new self($this->value - $other->value, $this->unit);
    }

    public function multiply(float $multiplier): self
    {
        return new self($this->value * $multiplier, $this->unit);
    }

    public function isEnoughFor(self $required): bool
    {
        $this->assertSameUnit($required);

        return $this->value >= $required->value;
    }

    public function isZero(): bool
    {
        return $this->value === 0.0;
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    public function toUnit(string $targetUnit): float
    {
        $conversions = [
            'pcs' => ['kg' => null, 'ltr' => null, 'm' => null],
            'kg' => ['g' => 1000, 'mg' => 1_000_000, 'lbs' => 2.20462],
            'g' => ['kg' => 0.001, 'mg' => 1000],
            'ltr' => ['ml' => 1000, 'gal' => 0.264172],
            'ml' => ['ltr' => 0.001],
            'm' => ['cm' => 100, 'mm' => 1000, 'ft' => 3.28084],
            'cm' => ['m' => 0.01],
            'mm' => ['m' => 0.001],
        ];

        if ($this->unit === $targetUnit) {
            return $this->value;
        }

        $factor = $conversions[$this->unit][$targetUnit] ?? null;

        if (is_null($factor)) {
            throw new InvalidArgumentException(
                "Cannot convert from {$this->unit} to {$targetUnit}"
            );
        }

        return $this->value * $factor;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'unit' => $this->unit,
        ];
    }

    public function __toString(): string
    {
        return $this->value . ' ' . $this->unit;
    }

    private function assertSameUnit(self $other): void
    {
        if ($this->unit !== $other->unit) {
            throw new InvalidArgumentException(
                "Unit mismatch: {$this->unit} cannot be combined with {$other->unit}"
            );
        }
    }
}
