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
    public function dashboard()
    {
        $total = Order::where('status', 'completed')->sum('total');
        $order_processing = Order::where('status', 'processing')->orderBy('updated_at', 'desc')->take(6)->get();
        $order_list =  Order::count();
        $product_list =  Product::count();
        $bestSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(price) as total_price'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')->take(6)
            ->get()
            ->map(function ($item) {
                return [
                    'product' => $item->product,
                    'sold_quantity' => $item->total_quantity,
                    'total_price' => $item->total_price
                ];
            });

        $products = Product::orderBy('updated_at', 'desc')->take(6)->get();

            // dd($bestSellingProducts);


        return view('welcome', compact('total', 'order_processing', 'order_list', 'product_list', 'bestSellingProducts', 'products'));
    }
}
