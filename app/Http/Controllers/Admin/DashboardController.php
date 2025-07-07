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
    // protected $dashboardService, $orderService;
    // public function __construct(DashboardService $dashboardService, OrderService $orderService)
    // {
    //     $this->dashboardService = $dashboardService;
    //     $this->orderService = $orderService;
    // }

    // public function dashboard(Request $request)
    // {
    //     $startDate = $request->get('startDate');
    //     $endDate = $request->get('endDate');

    //     $statistics = $this->dashboardService->getStatistics($startDate, $endDate);
    //     // $newestProducts = $this->dashboardService->getNewestProducts($startDate, $endDate);

    //     if ($request->ajax()) {
    //         return response()->json([
    //             'statistics' => $statistics,
    //             // 'newestProducts' => $newestProducts
    //         ]);
    //     }
    //     return view('admin.dashboard', compact('statistics'));
    // }

    public function dashboard(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->get('startDate');
            $endDate = $request->get('endDate');

            $statuses = [
                'pending',
                'confirmed_pending_production',
                'in_production',
                'produced_awaiting_completion',
                'completed_waiting_for_shipment',
                'shipped',
                'cancelled',
            ];

            $orderStatusCounts = [];
            foreach ($statuses as $status) {
                $orderStatusCounts[$status] = $this->getTotalOrdersByStatus([$status], $startDate, $endDate);
            }

            $totalSales = $this->getTotalSales($startDate, $endDate);
            $totalOrders = $this->getTotalOrders($startDate, $endDate);
            $totalTopupRequests = $this->getTotalTopupRequests($startDate, $endDate);
            $totalUsers = $this->getTotalUsers();
            $totalEmployees = $this->getTotalEmployees();

            return response()->json([
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'total_topup_requests' => $totalTopupRequests,
                'order_status_counts' => $orderStatusCounts,
                'total_users' => $totalUsers,
                'total_employees' => $totalEmployees,
            ]);
        }

        return view('admin.dashboard');
    }


    private function getTotalSales($startDate, $endDate)
    {
        $query = DB::table('orders')
            ->where(['status' => 'shipped', 'payment_status' => 'completed']);

        if ($startDate && $endDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->sum('total');
    }

    private function getTotalOrders($startDate, $endDate)
    {
        $query = DB::table('orders');

        if ($startDate && $endDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->count();
    }

    private function getTotalTopupRequests($startDate, $endDate)
    {
        $query = DB::table('wallet_transactions')->where('is_topup_request', 1);

        if ($startDate && $endDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->count();
    }

    private function getTotalOrdersByStatus(array $statuses, $startDate, $endDate)
    {
        $query = DB::table('orders')
            ->whereIn('status', $statuses);

        if ($startDate && $endDate) {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->count();
    }

    private function getTotalUsers()
    {
        return DB::table('users')->count();
    }

    private function getTotalEmployees()
    {
        return DB::table('employees')->count();
    }
}
