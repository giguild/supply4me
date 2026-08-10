<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderConfirmed;
use App\Services\StockReservationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReserveStockForOrder implements ShouldQueue
{
    public function __construct(
        protected StockReservationService $stockReservationService,
    ) {}

    public function handle(OrderConfirmed $event): void
    {
        $this->stockReservationService->reserveForOrder($event->order);
    }
}
