<?php

namespace App\ValueObjects;

use Illuminate\Support\Str;

final class PaymentReference
{
    private string $value;

    private string $prefix;

    private string $date;

    private string $uniqueId;

    public function __construct(string $value)
    {
        $this->value = strtoupper($value);
        $parts = $this->parse($this->value);
        $this->prefix = $parts['prefix'];
        $this->date = $parts['date'];
        $this->uniqueId = $parts['unique_id'];
    }

    public static function generate(): self
    {
        $date = now()->format('Ymd');
        $uniqueId = strtoupper(Str::random(4));

        return new self("PAY-{$date}-{$uniqueId}");
    }

    public function parse(string $reference): array
    {
        $parts = explode('-', $reference, 3);

        return [
            'prefix' => $parts[0] ?? '',
            'date' => $parts[1] ?? '',
            'unique_id' => $parts[2] ?? '',
        ];
    }

    public function format(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getFormattedDate(): ?string
    {
        $date = \DateTime::createFromFormat('Ymd', $this->date);

        return $date ? $date->format('Y-m-d') : null;
    }

    public function isValid(): bool
    {
        return $this->prefix === 'PAY'
            && preg_match('/^\d{8}$/', $this->date) === 1
            && strlen($this->uniqueId) === 4;
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
            'date' => $this->date,
            'unique_id' => $this->uniqueId,
        ];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
