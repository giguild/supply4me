<?php

namespace App\Providers;

use App\Policies\BranchPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DeliveryPolicy;
use App\Policies\DriverPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PriceListPolicy;
use App\Policies\ProductBrandPolicy;
use App\Policies\ProductCategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\ReportPolicy;
use App\Policies\SettingPolicy;
use App\Policies\StockAdjustmentPolicy;
use App\Policies\StockItemPolicy;
use App\Policies\StockTransferPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use App\Policies\WarehousePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Branch::class => BranchPolicy::class,
        User::class => UserPolicy::class,
        Customer::class => CustomerPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Product::class => ProductPolicy::class,
        ProductCategory::class => ProductCategoryPolicy::class,
        ProductBrand::class => ProductBrandPolicy::class,
        Order::class => OrderPolicy::class,
        Payment::class => PaymentPolicy::class,
        Invoice::class => InvoicePolicy::class,
        StockItem::class => StockItemPolicy::class,
        StockAdjustment::class => StockAdjustmentPolicy::class,
        StockTransfer::class => StockTransferPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        Delivery::class => DeliveryPolicy::class,
        Driver::class => DriverPolicy::class,
        PriceList::class => PriceListPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        Setting::class => SettingPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
