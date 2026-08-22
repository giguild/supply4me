<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\ProfileController;
use App\Http\Controllers\Inertia\SettingsController;
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
use App\Http\Controllers\Inertia\GrnController;
use App\Http\Controllers\Inertia\PickListController;
use App\Http\Controllers\Inertia\PackingListController;
use App\Http\Controllers\Inertia\ShipmentController;
use App\Http\Controllers\Inertia\DeliveryController;
use App\Http\Controllers\Inertia\DriverController;
use App\Http\Controllers\Inertia\DeliveryRouteController;
use App\Http\Controllers\Inertia\UserController;
use App\Http\Controllers\Inertia\CompanyController;
use App\Http\Controllers\Inertia\BranchController;
use App\Http\Controllers\Inertia\ReportController;
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

    Route::post('/account/addresses', [ShippingAddressController::class, 'store'])->name('storefront.addresses.store');
    Route::put('/account/addresses/{address}', [ShippingAddressController::class, 'update'])->name('storefront.addresses.update');
    Route::delete('/account/addresses/{address}', [ShippingAddressController::class, 'destroy'])->name('storefront.addresses.destroy');

    Route::post('/account/contacts', [CustomerContactController::class, 'store'])->name('storefront.contacts.store');
    Route::put('/account/contacts/{contact}', [CustomerContactController::class, 'update'])->name('storefront.contacts.update');
    Route::delete('/account/contacts/{contact}', [CustomerContactController::class, 'destroy'])->name('storefront.contacts.destroy');

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
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/contacts', [CustomerController::class, 'storeContact'])->name('customers.contacts.store');
    Route::put('customers/{customer}/contacts/{contact}', [CustomerController::class, 'updateContact'])->name('customers.contacts.update');
    Route::delete('customers/{customer}/contacts/{contact}', [CustomerController::class, 'destroyContact'])->name('customers.contacts.destroy');

    Route::resource('suppliers', SupplierController::class);

    Route::resource('products', ProductController::class);
    Route::resource('product-categories', ProductCategoryController::class)->except(['show', 'edit']);
    Route::resource('product-brands', ProductBrandController::class)->except(['show', 'edit']);
    Route::resource('product-units', ProductUnitController::class)->except(['show', 'edit']);

    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve')->middleware('permission:payment.approve');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject')->middleware('permission:payment.reject');
    Route::post('payments/{payment}/mark-partial', [PaymentController::class, 'markPartial'])->name('payments.markPartial')->middleware('permission:payment.approve');

    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/adjustments', [StockController::class, 'adjustments'])->name('stock.adjustments');
    Route::post('stock/adjustments', [StockController::class, 'storeAdjustment'])->name('stock.adjustments.store');
    Route::get('stock/transfers', [StockController::class, 'transfers'])->name('stock.transfers');
    Route::post('stock/transfers', [StockController::class, 'storeTransfer'])->name('stock.transfers.store');

    Route::resource('grn', GrnController::class);
    Route::resource('pick-lists', PickListController::class);
    Route::resource('packing-lists', PackingListController::class);

    Route::resource('shipments', ShipmentController::class);
    Route::get('shipments/{shipment}/track', [ShipmentController::class, 'track'])->name('shipments.track');

    Route::resource('deliveries', DeliveryController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('delivery-routes', DeliveryRouteController::class);
    Route::resource('users', UserController::class);
    Route::resource('companies', CompanyController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('branches', BranchController::class);

    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');

    Route::get('sales-rep', [SalesRepController::class, 'dashboard'])->name('sales-rep.dashboard');
    Route::get('sales-rep/customers', [SalesRepController::class, 'index'])->name('sales-rep.customers');
    Route::get('sales-reps', [SalesRepController::class, 'adminIndex'])->name('sales-reps.index');
    Route::get('sales-reps/{user}', [SalesRepController::class, 'show'])->name('sales-reps.show');
});
