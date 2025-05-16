<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Brand;
use App\Models\ConfigPayment;
use App\Policies\AttributePolicy;
use App\Policies\BrandPolicy;
use App\Policies\ConfigurationPaymentPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Category;
use App\Models\Config;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Policies\CataloguePolicy;
use App\Policies\ConfigurationPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\WalletTransactionPolicy;
use Attribute;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CataloguePolicy::class,
        Employee::class => EmployeePolicy::class,
        Role::class => RolePolicy::class,
        Product::class => ProductPolicy::class,
        Brand::class => BrandPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Config::class => ConfigurationPolicy::class,
        ConfigPayment::class => ConfigurationPaymentPolicy::class,
        User::class => CustomerPolicy::class,
        Attribute::class => AttributePolicy::class,
        Order::class => OrderPolicy::class,
        WalletTransaction::class => WalletTransactionPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('delete-model', fn(Employee $employee, $model) => $employee->isAdmin() || $employee->hasPermissionTo("$model Destroy"));
    }
}
