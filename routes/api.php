<?php

use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Branches\BranchController;
use App\Http\Controllers\Api\V1\Companies\CompanyController;
use App\Http\Controllers\Api\V1\Customers\CustomerAddressController;
use App\Http\Controllers\Api\V1\Customers\CustomerContactController;
use App\Http\Controllers\Api\V1\Customers\CustomerController;
use App\Http\Controllers\Api\V1\Delivery\DeliveryController;
use App\Http\Controllers\Api\V1\Delivery\DeliveryRouteController;
use App\Http\Controllers\Api\V1\Delivery\DriverController;
use App\Http\Controllers\Api\V1\Invoices\InvoiceController;
use App\Http\Controllers\Api\V1\Inventory\StockAdjustmentController;
use App\Http\Controllers\Api\V1\Inventory\StockController;
use App\Http\Controllers\Api\V1\Inventory\StockTransferController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
use App\Http\Controllers\Api\V1\Orders\OrderItemController;
use App\Http\Controllers\Api\V1\Payments\PaymentController;
use App\Http\Controllers\Api\V1\PickingPacking\PackingListController;
use App\Http\Controllers\Api\V1\PickingPacking\PickListController;
use App\Http\Controllers\Api\V1\Products\ProductBrandController;
use App\Http\Controllers\Api\V1\Products\ProductCategoryController;
use App\Http\Controllers\Api\V1\Products\ProductController;
use App\Http\Controllers\Api\V1\Receiving\GRNController;
use App\Http\Controllers\Api\V1\Reports\ReportController;
use App\Http\Controllers\Api\V1\Settings\SettingController;
use App\Http\Controllers\Api\V1\Shipping\ShipmentController;
use App\Http\Controllers\Api\V1\Suppliers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Supply4Me API v1 Routes
|
*/

Route::prefix('v1')->group(function () {

    // ── Auth (Public) ──────────────────────────────────────────────
    Route::post('auth/register', RegisterController::class);
    Route::post('auth/login', LoginController::class)->name('api.v1.login');
    Route::post('auth/forgot-password', ForgotPasswordController::class)->name('api.v1.forgot-password');

    // ── Authenticated Routes ───────────────────────────────────────
    Route::middleware('auth:sanctum')->name('api.v1.')->group(function () {

        // ── Companies ───────────────────────────────────────────
        Route::apiResource('companies', CompanyController::class);

        // ── Branches ────────────────────────────────────────────
        Route::apiResource('branches', BranchController::class);

        // ── Users ───────────────────────────────────────────────
        Route::apiResource('users', \App\Http\Controllers\Api\V1\Users\UserController::class);

        // ── Customers ───────────────────────────────────────────
        Route::apiResource('customers', CustomerController::class);

        // ── Customer Contacts ───────────────────────────────────
        Route::apiResource('customers.contacts', CustomerContactController::class)
            ->except(['show', 'create', 'edit']);

        // ── Customer Addresses ──────────────────────────────────
        Route::apiResource('customers.addresses', CustomerAddressController::class)
            ->except(['show', 'create', 'edit']);

        // ── Suppliers ───────────────────────────────────────────
        Route::apiResource('suppliers', SupplierController::class);

        // ── Products ────────────────────────────────────────────
        Route::apiResource('products', ProductController::class);

        // ── Product Categories ──────────────────────────────────
        Route::apiResource('product-categories', ProductCategoryController::class);

        // ── Product Brands ──────────────────────────────────────
        Route::apiResource('product-brands', ProductBrandController::class);

        // ── Orders ──────────────────────────────────────────────
        Route::apiResource('orders', OrderController::class);

        // Order Actions
        Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/hold', [OrderController::class, 'hold'])->name('orders.hold');
        Route::post('orders/{order}/release', [OrderController::class, 'release'])->name('orders.release');

        // ── Order Items ─────────────────────────────────────────
        Route::apiResource('orders.items', OrderItemController::class)
            ->except(['show', 'create', 'edit']);

        // ── Payments ────────────────────────────────────────────
        Route::apiResource('payments', PaymentController::class);

        // Payment Actions
        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve']);
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject']);
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund']);

        // ── Payment Allocations ─────────────────────────────────
        Route::get('payments/{payment}/allocations', [PaymentController::class, 'allocations']);
        Route::post('payments/{payment}/allocations', [PaymentController::class, 'allocate']);
        Route::delete('payments/{payment}/allocations/{allocation}', [PaymentController::class, 'destroyAllocation']);

        // ── Invoices ────────────────────────────────────────────
        Route::apiResource('invoices', InvoiceController::class);

        // Invoice Actions
        Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send']);
        Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void']);

        // ── Stock Items ─────────────────────────────────────────
        Route::get('stock-items', [StockController::class, 'index']);
        Route::get('stock-items/{stockItem}', [StockController::class, 'show']);

        // ── Stock Adjustments ───────────────────────────────────
        Route::apiResource('stock-adjustments', StockAdjustmentController::class)
            ->only(['index', 'store', 'show']);

        // Stock Adjustment Actions
        Route::post('stock-adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve']);
        Route::post('stock-adjustments/{adjustment}/reject', [StockAdjustmentController::class, 'reject']);

        // ── Stock Transfers ─────────────────────────────────────
        Route::apiResource('stock-transfers', StockTransferController::class)
            ->only(['index', 'store', 'show']);

        // Stock Transfer Actions
        Route::post('stock-transfers/{transfer}/approve', [StockTransferController::class, 'approve']);
        Route::post('stock-transfers/{transfer}/ship', [StockTransferController::class, 'ship']);
        Route::post('stock-transfers/{transfer}/receive', [StockTransferController::class, 'receive']);

        // ── GRN (Goods Received Notes) ──────────────────────────
        Route::apiResource('grn', GRNController::class);

        // GRN Actions
        Route::post('grn/{grn}/receive', [GRNController::class, 'receive']);
        Route::post('grn/{grn}/complete', [GRNController::class, 'complete']);

        // ── Pick Lists ──────────────────────────────────────────
        Route::apiResource('pick-lists', PickListController::class)
            ->only(['index', 'store', 'show', 'update']);

        // Pick List Actions
        Route::post('pick-lists/{pickList}/start', [PickListController::class, 'start']);
        Route::post('pick-lists/{pickList}/complete', [PickListController::class, 'complete']);

        // Pick List Items
        Route::post('pick-lists/{pickList}/items/{item}/pick', [PickListController::class, 'pickItem']);

        // ── Packing Lists ───────────────────────────────────────
        Route::apiResource('packing-lists', PackingListController::class)
            ->only(['index', 'store', 'show']);

        // Packing List Actions
        Route::post('packing-lists/{packingList}/pack', [PackingListController::class, 'pack']);
        Route::post('packing-lists/{packingList}/verify', [PackingListController::class, 'verify']);

        // ── Shipments ───────────────────────────────────────────
        Route::apiResource('shipments', ShipmentController::class)
            ->only(['index', 'store', 'show', 'update']);

        // Shipment Actions
        Route::post('shipments/{shipment}/ship', [ShipmentController::class, 'ship']);
        Route::post('shipments/{shipment}/deliver', [ShipmentController::class, 'deliver']);

        // ── Shipping Carriers ───────────────────────────────────
        Route::apiResource('shipping-carriers', \App\Http\Controllers\Api\V1\Shipping\ShippingCarrierController::class);

        // ── Deliveries ──────────────────────────────────────────
        Route::apiResource('deliveries', DeliveryController::class)
            ->only(['index', 'store', 'show', 'update']);

        // Delivery Actions
        Route::post('deliveries/{delivery}/assign-driver', [DeliveryController::class, 'assignDriver']);
        Route::post('deliveries/{delivery}/start', [DeliveryController::class, 'start']);
        Route::post('deliveries/{delivery}/complete', [DeliveryController::class, 'complete']);
        Route::post('deliveries/{delivery}/fail', [DeliveryController::class, 'fail']);

        // ── Drivers ─────────────────────────────────────────────
        Route::apiResource('drivers', DriverController::class);

        // ── Delivery Routes ─────────────────────────────────────
        Route::apiResource('delivery-routes', DeliveryRouteController::class)
            ->only(['index', 'store', 'show']);

        // Delivery Route Actions
        Route::post('delivery-routes/{route}/start', [DeliveryRouteController::class, 'start']);
        Route::post('delivery-routes/{route}/complete', [DeliveryRouteController::class, 'complete']);

        // Route Stops
        Route::post('delivery-routes/{route}/stops/{stop}/arrive', [DeliveryRouteController::class, 'arriveStop']);
        Route::post('delivery-routes/{route}/stops/{stop}/complete', [DeliveryRouteController::class, 'completeStop']);

        // ── Reports ─────────────────────────────────────────────
        Route::get('reports/sales', [ReportController::class, 'sales']);
        Route::get('reports/inventory', [ReportController::class, 'inventory']);
        Route::get('reports/financial', [ReportController::class, 'financial']);

        // ── Settings ────────────────────────────────────────────
        Route::get('settings', [SettingController::class, 'index']);
        Route::put('settings', [SettingController::class, 'update']);
    });
});
