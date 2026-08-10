<?php

namespace App\ValueObjects;

final class Email
{
    private string $value;

    private string $local;

    private string $domain;

    public function __construct(string $value)
    {
        $this->value = strtolower(trim($value));
        $parts = explode('@', $this->value, 2);
        $this->local = $parts[0] ?? '';
        $this->domain = $parts[1] ?? '';
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLocal(): string
    {
        return $this->local;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function local(): string
    {
        return $this->local;
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isValid(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'local' => $this->local,
            'domain' => $this->domain,
        ];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
