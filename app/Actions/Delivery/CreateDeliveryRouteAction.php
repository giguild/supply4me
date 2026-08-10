<?php

namespace App\Actions\Delivery;

use App\Enums\Delivery\RouteStatus;
use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryRoute;
use App\Models\Delivery\DeliveryRouteStop;

class CreateDeliveryRouteAction
{
    public function execute(array $data): DeliveryRoute
    {
        $route = DeliveryRoute::create([
            'company_id' => $data['company_id'],
            'driver_id' => $data['driver_id'],
            'date' => $data['date'],
            'status' => RouteStatus::Planned,
            'notes' => $data['notes'] ?? null,
        ]);

        if (! empty($data['delivery_ids']) && is_array($data['delivery_ids'])) {
            $sequence = 1;
            foreach ($data['delivery_ids'] as $deliveryId) {
                $delivery = Delivery::find($deliveryId);
                if ($delivery) {
                    DeliveryRouteStop::create([
                        'route_id' => $route->id,
                        'delivery_id' => $deliveryId,
                        'sequence' => $sequence,
                        'status' => 'pending',
                    ]);
                    $sequence++;
                }
            }
        }

        return $route;
    }
}
