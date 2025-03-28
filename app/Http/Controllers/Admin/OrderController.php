<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderService;
    protected $order;
    public function __construct(OrderService $orderService, Order $order)
    {
        $this->orderService = $orderService;
        $this->order = $order;
    }
    public function index()
    {
        $title = 'Đơn hàng';
        try {
            $orders = $this->orderService->getTodayOrder();
            return view('admin.order.index', compact('orders', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to fetch orders: ' . $e->getMessage());
            return redirect()->route('admin.order.index')->with('error', 'Failed to fetch orders');
        }
    }

    public function filterOrder(Request $request)
    {
        $phone = $request->input('phone');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $title = 'Đơn hàng';
        try {
            $orders = $this->orderService->filterOrder($startDate, $endDate, $phone);
            return view('admin.order.index', compact('orders', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to fetch orders by filter: ' . $e->getMessage());
            return redirect()->route('admin.order.index')->with('error', 'Failed to fetch orders by filter');
        }
    }

    public function detail($id)
    {
        $title = 'Chi tiết đơn hàng';
        try {
            $order = $this->orderService->getOrderbyID($id);
            if ($order->notification == 1) {
                $order->notification = 0;
                $order->save();
            }
            return view('admin.order.detail', compact('order', 'title'));
        } catch (\Exception $e) {
            Log::error('Failed to find order');
        }
    }
}
