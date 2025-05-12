<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\DashboardService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $dashboardService, $orderService;
    public function __construct(DashboardService $dashboardService, OrderService $orderService)
    {
        $this->dashboardService = $dashboardService;
        $this->orderService = $orderService;
    }

    public function dashboard(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        $statistics = $this->dashboardService->getStatistics($startDate, $endDate);
        $newestProducts = $this->dashboardService->getNewestProducts($startDate, $endDate);

        if ($request->ajax()) {
            return response()->json([
                'statistics' => $statistics,
                'newestProducts' => $newestProducts
            ]);
        }
        return view('admin.dashboard', compact('statistics', 'newestProducts'));
    }
}
