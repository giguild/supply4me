<?php

namespace App\Providers;

use App\Services\Inventory\ReorderService;
use App\Services\Inventory\StockMovementService;
use App\Services\Inventory\StockReservationService;
use App\Services\Invoice\InvoiceCalculationService;
use App\Services\Invoice\InvoiceGenerationService;
use App\Services\Order\OrderCalculationService;
use App\Services\Order\OrderFulfillmentService;
use App\Services\Payment\PaymentAllocationService;
use App\Services\Payment\PaymentProcessingService;
use App\Services\Pricing\DiscountService;
use App\Services\Pricing\PricingService;
use App\Services\Pricing\TaxService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PricingService::class, function ($app) {
            return new PricingService();
        });

        $this->app->singleton(TaxService::class, function ($app) {
            return new TaxService();
        });

        $this->app->singleton(DiscountService::class, function ($app) {
            return new DiscountService();
        });

        $this->app->singleton(OrderCalculationService::class, function ($app) {
            return new OrderCalculationService(
                $app->make(PricingService::class),
                $app->make(TaxService::class),
                $app->make(DiscountService::class)
            );
        });

        $this->app->singleton(StockReservationService::class, function ($app) {
            return new StockReservationService();
        });

        $this->app->singleton(StockMovementService::class, function ($app) {
            return new StockMovementService();
        });

        $this->app->singleton(ReorderService::class, function ($app) {
            return new ReorderService();
        });

        $this->app->singleton(InvoiceGenerationService::class, function ($app) {
            return new InvoiceGenerationService();
        });

        $this->app->singleton(InvoiceCalculationService::class, function ($app) {
            return new InvoiceCalculationService();
        });

        $this->app->singleton(PaymentProcessingService::class, function ($app) {
            return new PaymentProcessingService();
        });

        $this->app->singleton(PaymentAllocationService::class, function ($app) {
            return new PaymentAllocationService();
        });

        $this->app->singleton(OrderFulfillmentService::class, function ($app) {
            return new OrderFulfillmentService(
                $app->make(StockReservationService::class),
                $app->make(OrderCalculationService::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
