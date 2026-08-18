<?php

namespace App\Providers;

use App\Events\Companies\CompanyCreated;
use App\Events\Companies\CompanyUpdated;
use App\Events\Core\UserCreated;
use App\Events\Core\UserDeactivated;
use App\Events\Core\UserUpdated;
use App\Events\Customers\CreditStatusChanged;
use App\Events\Customers\CustomerCreated;
use App\Events\Customers\CustomerUpdated;
use App\Events\Delivery\DeliveryCompleted;
use App\Events\Delivery\DeliveryFailed;
use App\Events\Delivery\DriverAssigned;
use App\Events\Inventory\StockAdjusted;
use App\Events\Inventory\StockLow;
use App\Events\Inventory\StockOut;
use App\Events\Inventory\StockReserved;
use App\Events\Inventory\StockReleased;
use App\Events\Invoicing\InvoiceGenerated;
use App\Events\Invoicing\InvoiceOverdue;
use App\Events\Invoicing\InvoicePaid;
use App\Events\Orders\OrderCancelled;
use App\Events\Orders\OrderConfirmed;
use App\Events\Orders\OrderCreated;
use App\Events\Orders\OrderReadyForPickup;
use App\Events\Payments\PaymentApproved;
use App\Events\Payments\PaymentCompleted;
use App\Events\Payments\PaymentCreated;
use App\Events\Payments\PaymentRejected;
use App\Events\Payments\PaymentRefunded;
use App\Listeners\Core\LogUserActivity;
use App\Listeners\Core\SendWelcomeEmail;
use App\Listeners\Delivery\NotifyCustomerOfDelivery;
use App\Listeners\Delivery\NotifyDriverOfAssignment;
use App\Listeners\Delivery\UpdateOrderDeliveryStatus;
use App\Listeners\Inventory\CheckReorderLevel;
use App\Listeners\Inventory\LogStockMovement;
use App\Listeners\Inventory\NotifyLowStock;
use App\Listeners\Invoicing\CheckOverdueInvoices;
use App\Listeners\Invoicing\LogInvoiceActivity;
use App\Listeners\Invoicing\SendInvoiceEmail;
use App\Listeners\Orders\NotifyWarehouseOfOrder;
use App\Listeners\Orders\ReserveStockForOrder;
use App\Listeners\Orders\SendOrderConfirmation;
use App\Listeners\Payments\UpdateCustomerCredit;
use App\Listeners\Payments\UpdateInvoicePaymentStatus;
use App\Listeners\Payments\UpdateOrderPaymentStatus;
use App\Listeners\Payments\SendPaymentConfirmation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            SendWelcomeEmail::class,
            LogUserActivity::class,
        ],
        UserUpdated::class => [
            LogUserActivity::class,
        ],
        UserDeactivated::class => [
            LogUserActivity::class,
        ],
        CompanyCreated::class => [
            LogUserActivity::class,
        ],
        CompanyUpdated::class => [
            LogUserActivity::class,
        ],
        CustomerCreated::class => [
            LogUserActivity::class,
        ],
        CustomerUpdated::class => [
            LogUserActivity::class,
        ],
        CreditStatusChanged::class => [
            LogUserActivity::class,
        ],
        OrderCreated::class => [
            ReserveStockForOrder::class,
            SendOrderConfirmation::class,
            NotifyWarehouseOfOrder::class,
        ],
        OrderConfirmed::class => [
            SendOrderConfirmation::class,
        ],
        OrderCancelled::class => [
            LogUserActivity::class,
        ],
        OrderReadyForPickup::class => [
            NotifyWarehouseOfOrder::class,
        ],
        PaymentCreated::class => [
            LogUserActivity::class,
        ],
        PaymentApproved::class => [
            UpdateOrderPaymentStatus::class,
            UpdateInvoicePaymentStatus::class,
            SendPaymentConfirmation::class,
        ],
        PaymentRejected::class => [
            LogUserActivity::class,
        ],
        PaymentCompleted::class => [
            UpdateOrderPaymentStatus::class,
            UpdateInvoicePaymentStatus::class,
            UpdateCustomerCredit::class,
        ],
        PaymentRefunded::class => [
            UpdateOrderPaymentStatus::class,
            LogUserActivity::class,
        ],
        InvoiceGenerated::class => [
            SendInvoiceEmail::class,
            LogInvoiceActivity::class,
        ],
        InvoicePaid::class => [
            LogInvoiceActivity::class,
        ],
        InvoiceOverdue::class => [
            CheckOverdueInvoices::class,
        ],
        StockReserved::class => [
            LogStockMovement::class,
        ],
        StockReleased::class => [
            LogStockMovement::class,
        ],
        StockAdjusted::class => [
            LogStockMovement::class,
            CheckReorderLevel::class,
        ],
        StockLow::class => [
            NotifyLowStock::class,
        ],
        StockOut::class => [
            NotifyLowStock::class,
        ],
        DriverAssigned::class => [
            NotifyDriverOfAssignment::class,
        ],
        DeliveryCompleted::class => [
            UpdateOrderDeliveryStatus::class,
            NotifyCustomerOfDelivery::class,
        ],
        DeliveryFailed::class => [
            NotifyCustomerOfDelivery::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
