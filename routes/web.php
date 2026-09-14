<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\ProfileController;
use App\Http\Controllers\Inertia\SettingsController;
use App\Http\Controllers\Inertia\NotificationController;
use App\Http\Controllers\Inertia\CustomerController;
use App\Http\Controllers\Inertia\SupplierController;
use App\Http\Controllers\Inertia\ProductController;
use App\Http\Controllers\Inertia\ProductCategoryController;
use App\Http\Controllers\Inertia\ProductBrandController;
use App\Http\Controllers\Inertia\ProductUnitController;
use App\Http\Controllers\Inertia\OrderController;
use App\Http\Controllers\Inertia\InvoiceController;
use App\Http\Controllers\Inertia\PaymentController;
use App\Http\Controllers\Inertia\StockController;
use App\Http\Controllers\Inertia\WarehouseController;
use App\Http\Controllers\Inertia\GrnController;
use App\Http\Controllers\Inertia\PickListController;
use App\Http\Controllers\Inertia\PackingListController;
use App\Http\Controllers\Inertia\ShipmentController;
use App\Http\Controllers\Inertia\DeliveryController;
use App\Http\Controllers\Inertia\DriverController;
use App\Http\Controllers\Inertia\DeliveryRouteController;
use App\Http\Controllers\Inertia\ShippingCarrierController;
use App\Http\Controllers\Inertia\FeaturedProductController;
use App\Http\Controllers\Inertia\UserController;
use App\Http\Controllers\Inertia\CompanyController;
use App\Http\Controllers\Inertia\BranchController;
use App\Http\Controllers\Inertia\ReportController;
use App\Http\Controllers\Inertia\RoleController;
use App\Http\Controllers\Inertia\SalesRepController;
use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\StorefrontAuthController;
use App\Http\Controllers\Storefront\WishlistController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\ShippingAddressController;
use App\Http\Controllers\Storefront\CustomerContactController;
use Illuminate\Support\Facades\Route;

// ── Storefront (public - landing page) ───────────────────────
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.home');
Route::get('/shop', [StorefrontController::class, 'products'])->name('storefront.products');
Route::get('/product/{slug}', [StorefrontController::class, 'show'])->name('storefront.product');

Route::get('/cart', [CartController::class, 'index'])->name('storefront.cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('storefront.cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('storefront.cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('storefront.cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('storefront.cart.count');

Route::get('/register', [StorefrontAuthController::class, 'showRegister'])->name('storefront.register');
Route::post('/register', [StorefrontAuthController::class, 'register'])->name('storefront.register.post');
Route::get('/store-login', [StorefrontAuthController::class, 'showLogin'])->name('storefront.login');
Route::post('/store-login', [StorefrontAuthController::class, 'login'])->name('storefront.login.post');
Route::post('/store-logout', [StorefrontAuthController::class, 'logout'])->name('storefront.logout');

Route::middleware('auth:customer')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('storefront.checkout');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('storefront.placeOrder');
    Route::get('/payment/{invoice}', [CheckoutController::class, 'payment'])->name('storefront.payment');
    Route::post('/payment/{invoice}', [CheckoutController::class, 'submitPayment'])->name('storefront.submitPayment');
    Route::get('/order-confirmation/{order}', [CheckoutController::class, 'orderConfirmation'])->name('storefront.orderConfirmation');
    Route::get('/account', [CheckoutController::class, 'account'])->name('storefront.account');
    Route::get('/account/orders', [CheckoutController::class, 'orders'])->name('storefront.orders');
    Route::get('/account/invoices', [CheckoutController::class, 'invoices'])->name('storefront.invoices');
    Route::get('/account/payments', [CheckoutController::class, 'payments'])->name('storefront.payments');

    Route::post('/account/addresses', [ShippingAddressController::class, 'store'])->name('storefront.addresses.store');
    Route::put('/account/addresses/{address}', [ShippingAddressController::class, 'update'])->name('storefront.addresses.update');
    Route::delete('/account/addresses/{address}', [ShippingAddressController::class, 'destroy'])->name('storefront.addresses.destroy');

    Route::post('/account/contacts', [CustomerContactController::class, 'store'])->name('storefront.contacts.store');
    Route::put('/account/contacts/{contact}', [CustomerContactController::class, 'update'])->name('storefront.contacts.update');
    Route::delete('/account/contacts/{contact}', [CustomerContactController::class, 'destroy'])->name('storefront.contacts.destroy');

    Route::post('/account/avatar', [CheckoutController::class, 'uploadAvatar'])->name('storefront.avatar');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('storefront.wishlist');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('storefront.wishlist.toggle');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('storefront.wishlist.destroy');
});

// ── Internal ERP Auth ────────────────────────────────────────
Route::get('/erp/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/erp/login', [LoginController::class, 'loginWeb'])->name('login.post');
Route::post('/erp/logout', [LoginController::class, 'logout'])->name('logout');

// ── Internal ERP (protected) ─────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/erp', DashboardController::class)->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::put('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('role:super_admin');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('role:super_admin');
    Route::get('settings/payment-info', [SettingsController::class, 'paymentInfo'])->name('settings.payment-info')->middleware('role:super_admin');
    Route::put('settings/payment-info', [SettingsController::class, 'updatePaymentInfo'])->name('settings.payment-info.update')->middleware('role:super_admin');

    // Roles
    Route::resource('roles', RoleController::class)->middleware('role:super_admin');

    // Customers
    Route::resource('customers', CustomerController::class, [
        'middleware_for' => [
            'index' => ['permission:customer.view'],
            'create' => ['permission:customer.create'],
            'store' => ['permission:customer.create'],
            'show' => ['permission:customer.view'],
            'edit' => ['permission:customer.update'],
            'update' => ['permission:customer.update'],
            'destroy' => ['permission:customer.delete'],
        ],
    ]);
    Route::post('customers/{customer}/contacts', [CustomerController::class, 'storeContact'])->name('customers.contacts.store')->middleware('permission:customer.create');
    Route::put('customers/{customer}/contacts/{contact}', [CustomerController::class, 'updateContact'])->name('customers.contacts.update')->middleware('permission:customer.update');
    Route::delete('customers/{customer}/contacts/{contact}', [CustomerController::class, 'destroyContact'])->name('customers.contacts.destroy')->middleware('permission:customer.delete');

    // Suppliers
    Route::resource('suppliers', SupplierController::class, [
        'middleware_for' => [
            'index' => ['permission:supplier.view'],
            'create' => ['permission:supplier.create'],
            'store' => ['permission:supplier.create'],
            'show' => ['permission:supplier.view'],
            'edit' => ['permission:supplier.update'],
            'update' => ['permission:supplier.update'],
            'destroy' => ['permission:supplier.delete'],
        ],
    ]);

    // Products
    Route::resource('products', ProductController::class, [
        'middleware_for' => [
            'index' => ['permission:product.view'],
            'create' => ['permission:product.create'],
            'store' => ['permission:product.create'],
            'show' => ['permission:product.view'],
            'edit' => ['permission:product.update'],
            'update' => ['permission:product.update'],
            'destroy' => ['permission:product.delete'],
        ],
    ]);
    Route::resource('product-categories', ProductCategoryController::class, [
        'except' => ['show', 'edit'],
        'middleware_for' => [
            'index' => ['permission:product.view-category'],
            'store' => ['permission:product.manage-category'],
            'update' => ['permission:product.manage-category'],
            'destroy' => ['permission:product.manage-category'],
        ],
    ]);
    Route::resource('product-brands', ProductBrandController::class, [
        'except' => ['show', 'edit'],
        'middleware_for' => [
            'index' => ['permission:product.view-brand'],
            'store' => ['permission:product.manage-brand'],
            'update' => ['permission:product.manage-brand'],
            'destroy' => ['permission:product.manage-brand'],
        ],
    ]);
    Route::resource('product-units', ProductUnitController::class, [
        'except' => ['show', 'edit'],
        'middleware_for' => [
            'index' => ['permission:product.view'],
            'store' => ['permission:product.create'],
            'update' => ['permission:product.update'],
            'destroy' => ['permission:product.delete'],
        ],
    ]);

    // Orders
    Route::resource('orders', OrderController::class, [
        'middleware_for' => [
            'index' => ['permission:order.view'],
            'create' => ['permission:order.create'],
            'store' => ['permission:order.create'],
            'show' => ['permission:order.view'],
            'edit' => ['permission:order.update'],
            'update' => ['permission:order.update'],
            'destroy' => ['permission:order.delete'],
        ],
    ]);
    Route::post('orders/{order}/pending', [OrderController::class, 'pending'])->name('orders.pending')->middleware('permission:order.update');
    Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm')->middleware('permission:order.confirm');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel')->middleware('permission:order.cancel');

    // Invoices
    Route::resource('invoices', InvoiceController::class, [
        'middleware_for' => [
            'index' => ['permission:invoice.view'],
            'create' => ['permission:invoice.create'],
            'store' => ['permission:invoice.create'],
            'show' => ['permission:invoice.view'],
            'edit' => ['permission:invoice.update'],
            'update' => ['permission:invoice.update'],
            'destroy' => ['permission:invoice.delete'],
        ],
    ]);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send')->middleware('permission:invoice.send');
    Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void')->middleware('permission:invoice.void');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download')->middleware('permission:invoice.view');
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store')->middleware('permission:invoice.create');

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('permission:payment.view');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show')->middleware('permission:payment.view');
    Route::get('payments/{payment}/download', [PaymentController::class, 'download'])->name('payments.download')->middleware('permission:payment.view');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store')->middleware('permission:payment.create');
    Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update')->middleware('permission:payment.update');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy')->middleware('permission:payment.delete');
    Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve')->middleware('permission:payment.approve');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject')->middleware('permission:payment.reject');
    Route::post('payments/{payment}/mark-partial', [PaymentController::class, 'markPartial'])->name('payments.markPartial')->middleware('permission:payment.approve');

    // Stock
    Route::get('stock', [StockController::class, 'index'])->name('stock.index')->middleware('permission:stock.view');
    Route::get('stock/adjustments', [StockController::class, 'adjustments'])->name('stock.adjustments')->middleware('permission:stock.view');
    Route::get('stock/adjustments/create', [StockController::class, 'adjustmentsCreate'])->name('stock.adjustments.create')->middleware('permission:stock.adjust');
    Route::post('stock/adjustments', [StockController::class, 'storeAdjustment'])->name('stock.adjustments.store')->middleware('permission:stock.adjust');
    Route::post('stock/adjustments/{adjustment}/approve', [StockController::class, 'approveAdjustment'])->name('stock.adjustments.approve')->middleware('permission:stock.manage');
    Route::post('stock/adjustments/{adjustment}/reject', [StockController::class, 'rejectAdjustment'])->name('stock.adjustments.reject')->middleware('permission:stock.manage');
    Route::get('stock/transfers', [StockController::class, 'transfers'])->name('stock.transfers')->middleware('permission:stock.view');
    Route::get('stock/transfers/create', [StockController::class, 'transfersCreate'])->name('stock.transfers.create')->middleware('permission:stock.transfer');
    Route::post('stock/transfers', [StockController::class, 'storeTransfer'])->name('stock.transfers.store')->middleware('permission:stock.transfer');
    Route::post('stock/transfers/{transfer}/approve', [StockController::class, 'approveTransfer'])->name('stock.transfers.approve')->middleware('permission:stock.manage');
    Route::post('stock/transfers/{transfer}/ship', [StockController::class, 'shipTransfer'])->name('stock.transfers.ship')->middleware('permission:stock.manage');
    Route::post('stock/transfers/{transfer}/receive', [StockController::class, 'receiveTransfer'])->name('stock.transfers.receive')->middleware('permission:stock.manage');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class, [
        'middleware_for' => [
            'index' => ['permission:stock.view'],
            'create' => ['permission:stock.manage'],
            'store' => ['permission:stock.manage'],
            'show' => ['permission:stock.view'],
            'edit' => ['permission:stock.manage'],
            'update' => ['permission:stock.manage'],
            'destroy' => ['permission:stock.manage'],
        ],
    ]);

    // GRN
    Route::resource('grn', GrnController::class, [
        'middleware_for' => [
            'index' => ['permission:grn.view'],
            'create' => ['permission:grn.create'],
            'store' => ['permission:grn.create'],
            'show' => ['permission:grn.view'],
            'edit' => ['permission:grn.update'],
            'update' => ['permission:grn.update'],
            'destroy' => ['permission:grn.manage'],
        ],
    ]);

    // Pick Lists
    Route::resource('pick-lists', PickListController::class, [
        'middleware_for' => [
            'index' => ['permission:picklist.view'],
            'create' => ['permission:picklist.create'],
            'store' => ['permission:picklist.create'],
            'show' => ['permission:picklist.view'],
            'edit' => ['permission:picklist.update'],
            'update' => ['permission:picklist.update'],
            'destroy' => ['permission:picklist.manage'],
        ],
    ]);
    Route::post('pick-lists/{pickList}/start', [PickListController::class, 'start'])->name('pick-lists.start')->middleware('permission:picklist.update');
    Route::post('pick-lists/{pickList}/complete', [PickListController::class, 'complete'])->name('pick-lists.complete')->middleware('permission:picklist.complete');

    // Packing Lists
    Route::resource('packing-lists', PackingListController::class, [
        'middleware_for' => [
            'index' => ['permission:packinglist.view'],
            'create' => ['permission:packinglist.create'],
            'store' => ['permission:packinglist.create'],
            'show' => ['permission:packinglist.view'],
            'edit' => ['permission:packinglist.update'],
            'update' => ['permission:packinglist.update'],
            'destroy' => ['permission:packinglist.manage'],
        ],
    ]);
    Route::post('packing-lists/{packingList}/pack', [PackingListController::class, 'pack'])->name('packing-lists.pack')->middleware('permission:packinglist.update');
    Route::post('packing-lists/{packingList}/verify', [PackingListController::class, 'verify'])->name('packing-lists.verify')->middleware('permission:packinglist.complete');

    // Shipments
    Route::resource('shipments', ShipmentController::class, [
        'middleware_for' => [
            'index' => ['permission:shipment.view'],
            'create' => ['permission:shipment.create'],
            'store' => ['permission:shipment.create'],
            'show' => ['permission:shipment.view'],
            'edit' => ['permission:shipment.update'],
            'update' => ['permission:shipment.update'],
            'destroy' => ['permission:shipment.manage'],
        ],
    ]);
    Route::get('shipments/{shipment}/track', [ShipmentController::class, 'track'])->name('shipments.track')->middleware('permission:shipment.track');

    // Deliveries
    Route::resource('deliveries', DeliveryController::class, [
        'middleware_for' => [
            'index' => ['permission:delivery.view'],
            'create' => ['permission:delivery.create'],
            'store' => ['permission:delivery.create'],
            'show' => ['permission:delivery.view'],
            'edit' => ['permission:delivery.update'],
            'update' => ['permission:delivery.update'],
            'destroy' => ['permission:delivery.manage'],
        ],
    ]);

    // Drivers
    Route::resource('drivers', DriverController::class, [
        'middleware_for' => [
            'index' => ['permission:delivery.view'],
            'create' => ['permission:delivery.create'],
            'store' => ['permission:delivery.create'],
            'show' => ['permission:delivery.view'],
            'edit' => ['permission:delivery.update'],
            'update' => ['permission:delivery.update'],
            'destroy' => ['permission:delivery.manage'],
        ],
    ]);

    // Delivery Routes
    Route::resource('delivery-routes', DeliveryRouteController::class, [
        'middleware_for' => [
            'index' => ['permission:delivery.view-routes'],
            'create' => ['permission:delivery.manage-routes'],
            'store' => ['permission:delivery.manage-routes'],
            'show' => ['permission:delivery.view-routes'],
            'edit' => ['permission:delivery.manage-routes'],
            'update' => ['permission:delivery.manage-routes'],
            'destroy' => ['permission:delivery.manage-routes'],
        ],
    ]);

    // Shipping Carriers
    Route::resource('shipping-carriers', ShippingCarrierController::class, [
        'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy'],
        'middleware_for' => [
            'index' => ['permission:shipment.view'],
            'create' => ['permission:shipment.manage'],
            'store' => ['permission:shipment.manage'],
            'edit' => ['permission:shipment.manage'],
            'update' => ['permission:shipment.manage'],
            'destroy' => ['permission:shipment.manage'],
        ],
    ]);

    // Featured Products
    Route::resource('featured-products', FeaturedProductController::class, [
        'only' => ['index', 'store', 'update', 'destroy'],
        'middleware_for' => [
            'index' => ['permission:product.view'],
            'store' => ['permission:product.update'],
            'update' => ['permission:product.update'],
            'destroy' => ['permission:product.update'],
        ],
    ]);

    // Users
    Route::resource('users', UserController::class, [
        'middleware_for' => [
            'index' => ['permission:user.view'],
            'create' => ['permission:user.create'],
            'store' => ['permission:user.create'],
            'show' => ['permission:user.view'],
            'edit' => ['permission:user.update'],
            'update' => ['permission:user.update'],
            'destroy' => ['permission:user.delete'],
        ],
    ]);
    Route::post('users/{user}/avatar', [UserController::class, 'uploadAvatar'])->name('users.avatar')->middleware('permission:user.update');

    // Companies
    Route::resource('companies', CompanyController::class, [
        'only' => ['index', 'show', 'edit', 'update'],
        'middleware_for' => [
            'index' => ['permission:company.view'],
            'show' => ['permission:company.view'],
            'edit' => ['permission:company.update'],
            'update' => ['permission:company.update'],
        ],
    ]);

    // Branches
    Route::resource('branches', BranchController::class, [
        'middleware_for' => [
            'index' => ['permission:branch.view'],
            'create' => ['permission:branch.create'],
            'store' => ['permission:branch.create'],
            'show' => ['permission:branch.view'],
            'edit' => ['permission:branch.update'],
            'update' => ['permission:branch.update'],
            'destroy' => ['permission:branch.delete'],
        ],
    ]);

    // Reports
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales')->middleware('permission:report.view-sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory')->middleware('permission:report.view-inventory');
    Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial')->middleware('permission:report.view-financial');

    // Sales Rep
    Route::get('sales-rep', [SalesRepController::class, 'dashboard'])->name('sales-rep.dashboard');
    Route::get('sales-rep/customers', [SalesRepController::class, 'index'])->name('sales-rep.customers');
    Route::get('sales-reps', [SalesRepController::class, 'adminIndex'])->name('sales-reps.index')->middleware('permission:customer.view');
    Route::get('sales-reps/{user}', [SalesRepController::class, 'show'])->name('sales-reps.show')->middleware('permission:customer.view');
});
