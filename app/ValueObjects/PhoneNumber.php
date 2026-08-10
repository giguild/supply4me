<?php

namespace App\ValueObjects;

final class PhoneNumber
{
    private string $number;

    private string $countryCode;

    public function __construct(string $number, string $countryCode = '+1')
    {
        $this->number = preg_replace('/[^0-9]/', '', $number);
        $this->countryCode = $countryCode;
    }

    public static function from(string $number, string $countryCode = '+1'): self
    {
        return new self($number, $countryCode);
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function format(string $format = 'national'): string
    {
        $length = strlen($this->number);

        if ($format === 'e164') {
            return $this->toE164();
        }

        if ($length === 10) {
            return '(' . substr($this->number, 0, 3) . ') '
                . substr($this->number, 3, 3) . '-'
                . substr($this->number, 6);
        }

        if ($length === 11) {
            return '+' . $this->number[0] . ' ('
                . substr($this->number, 1, 3) . ') '
                . substr($this->number, 4, 3) . '-'
                . substr($this->number, 7);
        }

        return $this->number;
    }

    public function toE164(): string
    {
        $cleanCode = preg_replace('/[^0-9+]/', '', $this->countryCode);

        return '+' . ltrim($cleanCode, '+') . $this->number;
    }

    public function isValid(): bool
    {
        $length = strlen($this->number);

        return $length >= 7 && $length <= 15;
    }

    public function equals(self $other): bool
    {
        return $this->toE164() === $other->toE164();
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'country_code' => $this->countryCode,
            'e164' => $this->toE164(),
        ];
    }

    public function __toString(): string
    {
        return $this->toE164();
    }
}
