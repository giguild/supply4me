<?php

namespace App\ValueObjects;

final class OrderNumber
{
    private string $prefix;

    private string $sequence;

    private string $value;

    public function __construct(string $value)
    {
        $this->value = strtoupper($value);
        $parts = $this->parse($this->value);
        $this->prefix = $parts['prefix'];
        $this->sequence = $parts['sequence'];
    }

    public static function fromPrefixAndSequence(string $prefix, int $sequence): self
    {
        return new self($prefix . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT));
    }

    public static function generate(int $sequence): self
    {
        return self::fromPrefixAndSequence('ORD', $sequence);
    }

    public function parse(string $number): array
    {
        $parts = explode('-', $number, 2);

        return [
            'prefix' => $parts[0] ?? '',
            'sequence' => $parts[1] ?? '',
        ];
    }

    public function format(): string
    {
        return $this->prefix . '-' . $this->sequence;
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

    public function getSequenceNumber(): int
    {
        return (int) $this->sequence;
    }

    public function isValid(): bool
    {
        return $this->prefix === 'ORD'
            && preg_match('/^\d{6,}$/', $this->sequence) === 1;
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
        return $this->format();
    }
}
