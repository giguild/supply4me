<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private float $amount;

    private string $currency;

    public function __construct(float $amount, string $currency = 'USD')
    {
        $this->amount = round($amount, 2);
        $this->currency = strtoupper($currency);
    }

    public static function from(float $amount, string $currency = 'USD'): self
    {
        return new self($amount, $currency);
    }

    public static function zero(string $currency = 'USD'): self
    {
        return new self(0, $currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function isZero(): bool
    {
        return $this->amount === 0.0;
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function format(int $decimals = 2): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'NGN' => '₦',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency . ' ';

        return $symbol . number_format($this->amount, $decimals, '.', ',');
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Currency mismatch: {$this->currency} cannot be combined with {$other->currency}"
            );
        }
    }
}
