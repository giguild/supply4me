<?php

namespace App\Services\Delivery;

use App\Models\Delivery\Delivery;
use App\Models\Delivery\Driver;
use Illuminate\Support\Facades\Cache;

class DeliveryTrackingService
{
    /**
     * Update the current location of a driver.
     */
    public function updateLocation(Driver $driver, float $latitude, float $longitude): void
    {
        $locationData = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'updated_at' => now()->toIso8601String(),
        ];

        Cache::put(
            "driver_location_{$driver->id}",
            $locationData,
            now()->addHours(24)
        );
    }

    /**
     * Get the current location of a driver.
     */
    public function getDriverLocation(Driver $driver): array
    {
        $location = Cache::get("driver_location_{$driver->id}");

        if (!$location) {
            return [
                'latitude' => null,
                'longitude' => null,
                'updated_at' => null,
            ];
        }

        return $location;
    }

    /**
     * Get the current status of a delivery.
     */
    public function getDeliveryStatus(Delivery $delivery): string
    {
        return $delivery->status->label();
    }
}
