<?php

namespace App\Providers;

// use App\Http\View\Composers\NotificationComposer;
use App\Models\Collection;
use App\Models\Config;
use App\Models\Order;
use App\Models\Wallet;
use App\Observers\ActivityLogObserver;
use App\Services\CategoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Lấy danh sách tất cả các Model
        $models = File::allFiles(app_path('Models'));

        // Đăng ký Observer cho từng Model
        foreach ($models as $model) {
            $modelName = 'App\\Models\\' . $model->getFilenameWithoutExtension();
            if (class_exists($modelName)) {
                $modelName::observe(ActivityLogObserver::class);
            }
        }

        Carbon::setLocale('vi');

        $categoryService = app(CategoryService::class);

        View::composer('frontend.master', function ($view) use ($categoryService) {
            $collections = Collection::query()->select('id', 'name', 'slug')->orderBy('name', 'asc')->get();
            $categories = $categoryService->getAllCategoryIsParent();
            $view->with(['collections' => $collections, 'categories' => $categories]);
        });

        View::composer('*', function ($view) {
            $wallet = null;
            $user = null;

            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();

                // Chỉ áp dụng nếu không phải admin
                if ($user->role !== 'admin') {
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $user->id],
                        ['balance' => 0]
                    );
                }
            }

            $config = Config::query()->firstOrCreate();

            $view->with([
                'config' => $config,
                'wallet' => $wallet,
                'authUser' => $user
            ]);
        });

        $statuses = [
            'pending',
            'confirmed_pending_production',
            'in_production',
            'produced_awaiting_completion',
            'completed_waiting_for_shipment',
            'shipped',
            'cancelled',
        ];

        $ordersCountByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->pluck('total', 'status');

        $result = collect($statuses)->mapWithKeys(function ($status) use ($ordersCountByStatus) {
            return [$status => $ordersCountByStatus->get($status, 0)];
        });

        view()->composer('admin.layout.sidebar', function ($view) use ($result) {
            $view->with(['result' => $result]);
        });
    }
}
