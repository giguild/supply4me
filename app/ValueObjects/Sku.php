<?php

namespace App\ValueObjects;

use Illuminate\Support\Str;

final class Sku
{
    private string $value;

    private string $prefix;

    private string $sequence;

    public function __construct(string $value)
    {
        $this->value = strtoupper($value);
        $parts = $this->parse($this->value);
        $this->prefix = $parts['prefix'];
        $this->sequence = $parts['sequence'];
    }

    public static function from(string $prefix, string $sequence): self
    {
        return new self($prefix . '-' . $sequence);
    }

    public static function generate(string $prefix = 'SKU'): self
    {
        $sequence = strtoupper(Str::random(8));

        return new self($prefix . '-' . $sequence);
    }

    public function parse(string $sku): array
    {
        $parts = explode('-', $sku, 2);

        return [
            'prefix' => $parts[0] ?? '',
            'sequence' => $parts[1] ?? '',
        ];
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getSequence(): string
    {
        return $this->sequence;
    }

    public function isValid(): bool
    {
        return ! empty($this->prefix) && ! empty($this->sequence)
            && preg_match('/^[A-Z0-9]+$/', $this->value) === 1;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'prefix' => $this->prefix,
            'sequence' => $this->sequence,
        ];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
