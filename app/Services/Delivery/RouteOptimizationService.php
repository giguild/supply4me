<?php

namespace App\Services\Delivery;

use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryRoute;
use App\Models\Delivery\DeliveryRouteStop;
use App\ValueObjects\Address;
use Carbon\Carbon;

class RouteOptimizationService
{
    /**
     * Optimize a delivery route by reordering stops for efficiency.
     */
    public function optimizeRoute(DeliveryRoute $route): DeliveryRoute
    {
        $stops = $route->stops()->with('delivery.customer')->get();

        if ($stops->count() <= 1) {
            return $route;
        }

        $optimizedOrder = $this->nearestNeighborSort($stops);

        foreach ($optimizedOrder as $sequence => $stop) {
            $stop->update(['sequence' => $sequence + 1]);
        }

        $totalDistance = $this->calculateRouteDistance($optimizedOrder);

        $route->update([
            'total_distance' => $totalDistance,
            'total_time' => $this->estimateRouteTime($totalDistance),
        ]);

        return $route->fresh();
    }

    /**
     * Calculate distance between two addresses using the Haversine formula.
     */
    public function calculateDistance(Address $from, Address $to): float
    {
        $lat1 = $from->getLatitude() ?? 0;
        $lon1 = $from->getLongitude() ?? 0;
        $lat2 = $to->getLatitude() ?? 0;
        $lon2 = $to->getLongitude() ?? 0;

        if ($lat1 === 0 && $lon1 === 0 && $lat2 === 0 && $lon2 === 0) {
            return 0;
        }

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Estimate arrival time for a delivery route.
     */
    public function estimateArrivalTime(DeliveryRoute $route): Carbon
    {
        $totalTimeHours = (float) $route->total_time / 60;

        $startTime = $route->started_at ?? now();

        return $startTime->addHours($totalTimeHours);
    }

    /**
     * Sort stops using nearest neighbor algorithm.
     */
    private function nearestNeighborSort($stops): \Illuminate\Support\Collection
    {
        $sorted = collect();
        $remaining = $stops->keyBy('id');
        $currentLat = 0;
        $currentLon = 0;

        while ($remaining->isNotEmpty()) {
            $nearest = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($remaining as $stop) {
                $delivery = $stop->delivery;
                $customer = $delivery?->customer;

                if (!$customer) {
                    continue;
                }

                $stopLat = (float) ($customer->latitude ?? 0);
                $stopLon = (float) ($customer->longitude ?? 0);

                $distance = $this->calculateDistance(
                    Address::fromArray(['street' => '', 'city' => '', 'state' => '', 'postal_code' => '', 'country' => '', 'latitude' => $currentLat, 'longitude' => $currentLon]),
                    Address::fromArray(['street' => '', 'city' => '', 'state' => '', 'postal_code' => '', 'country' => '', 'latitude' => $stopLat, 'longitude' => $stopLon])
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $stop;
                }
            }

            if ($nearest) {
                $sorted->push($nearest);
                $remaining->pull($nearest->id);

                $customer = $nearest->delivery?->customer;
                if ($customer) {
                    $currentLat = (float) ($customer->latitude ?? 0);
                    $currentLon = (float) ($customer->longitude ?? 0);
                }
            } else {
                break;
            }
        }

        return $sorted;
    }

    /**
     * Calculate total distance for ordered stops.
     */
    private function calculateRouteDistance($stops): float
    {
        $totalDistance = 0;
        $previousAddress = null;

        foreach ($stops as $stop) {
            $customer = $stop->delivery?->customer;

            if (!$customer) {
                continue;
            }

            $currentAddress = Address::fromArray([
                'street' => $customer->address_line_1 ?? '',
                'city' => $customer->city ?? '',
                'state' => $customer->state ?? '',
                'postal_code' => $customer->postal_code ?? '',
                'country' => $customer->country ?? '',
                'latitude' => (float) $customer->latitude,
                'longitude' => (float) $customer->longitude,
            ]);

            if ($previousAddress) {
                $totalDistance += $this->calculateDistance($previousAddress, $currentAddress);
            }

            $previousAddress = $currentAddress;
        }

        return $totalDistance;
    }

    /**
     * Estimate travel time in minutes based on distance.
     */
    private function estimateRouteTime(float $distanceKm): float
    {
        $averageSpeedKmh = 30;

        return ($distanceKm / $averageSpeedKmh) * 60;
    }
}
