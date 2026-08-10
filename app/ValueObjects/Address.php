<?php

namespace App\ValueObjects;

final class Address
{
    private string $street;

    private string $city;

    private string $state;

    private string $postalCode;

    private string $country;

    private ?float $latitude;

    private ?float $longitude;

    public function __construct(
        string $street,
        string $city,
        string $state,
        string $postalCode,
        string $country,
        ?float $latitude = null,
        ?float $longitude = null,
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->postalCode = $postalCode;
        $this->country = strtoupper($country);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            street: $data['street'] ?? '',
            city: $data['city'] ?? '',
            state: $data['state'] ?? '',
            postalCode: $data['postal_code'] ?? '',
            country: $data['country'] ?? '',
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
        );
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function toString(): string
    {
        $parts = [
            $this->street,
            $this->city,
            $this->state,
            $this->postalCode,
            $this->country,
        ];

        return implode(', ', array_filter($parts));
    }

    public function isValid(): bool
    {
        return ! empty($this->street)
            && ! empty($this->city)
            && ! empty($this->state)
            && ! empty($this->postalCode)
            && ! empty($this->country);
    }

    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
