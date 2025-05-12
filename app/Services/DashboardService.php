<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected $order;
    protected $product;
    protected $client;
    protected $orderDetail;
    public $currentYear;
    public $currentDate;
    public $currentMonth;
    protected $user;
    public function __construct(Order $order, Client $client, Product $product, OrderDetail $orderDetail, User $user)
    {
        $this->order = $order;
        $this->client = $client;
        $this->product = $product;
        $this->orderDetail = $orderDetail;
        $this->user = $user;
        $this->currentYear = date("Y");
        $this->currentDate = date("d/m/Y");
        $this->currentMonth = date("m/Y");
    }

    public function getClientNumber()
    {
        try {
            return $this->client->whereYear('created_at', '=', $this->currentYear)->count();
        } catch (Exception $e) {
            Log::error("Failed to get client: " . $e->getMessage());
            throw new Exception('Failed to get client number');
        }
    }

    public function getOrderNumber()
    {
        try {
            return $this->order->whereYear('created_at', '=', $this->currentYear)->count();
        } catch (Exception $e) {
            Log::error('Failed to get order number: ' . $e->getMessage());
            throw new Exception('Failed to get order number');
        }
    }

    public function getAmountNumber()
    {
        try {
            return $this->order->whereYear('created_at', '=', $this->currentYear)->sum('total_money');
        } catch (Exception $e) {
            Log::error('Failed to calculate: ' . $e->getMessage());
            throw new Exception('Failed to calculate');
        }
    }
    public function getDailySale()
    {
        try {
            $income = $this->order->whereDate('created_at', '=', date('Y-m-d'))->sum('total_money');
            $amount = $this->order->whereDate('created_at', '=', date('Y-m-d'))->count();
            $orders = $this->order->whereDate('created_at', '=', date('Y-m-d'))->get();
            $interest = 0;
            $principal = 0;
            $sum = 0;
            foreach ($orders as $key => $value) {
                $sum += $value->total_money;
                foreach ($value->orderdetail as $key => $item) {
                    $principal += $item->product->price * $item->quantity;
                }
            }
            if ($sum == 0) {
                $moneyinterest = 0;
                $interest = 0;
            } else {
                $moneyinterest = $sum - $principal;
                $interest = $moneyinterest / $sum;
            }

            return [
                'income' => number_format($income, 0, ',', '.') . ' VND',
                'amount' => $amount,
                'moneyinterest' => number_format($moneyinterest, 0, ',', '.') . ' VND',
                'interest' => number_format($interest * 100, 1) . '%'
            ];
        } catch (Exception $e) {
            Log::error('Failed to calculate daily income: ' . $e->getMessage());
            throw new Exception('Failed to calculate daily income');
        }
    }


    public function getNewestClient()
    {
        try {
            $newClient = $this->client
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
            return $newClient;
        } catch (\Exception $e) {
            Log::error('Failed to get new client' . $e->getMessage());
            throw new Exception('Failed to get new client');
        }
    }
    public function getNewestStaff()
    {
        try {
            $newStaff = $this->user
                ->where('role_id', 2)
                ->orderByDesc('created_at', $this->currentMonth)
                ->limit(6)
                ->get();
            return $newStaff;
        } catch (\Exception $e) {
            Log::error('Failed to get new staff' . $e->getMessage());
            throw new Exception('Failed to get new staff');
        }
    }
    public function getNewestOrder()
    {
        try {
            $newOrder = $this->order
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();
            return $newOrder;
        } catch (\Exception $e) {
            Log::error('Failed to get new order' . $e->getMessage());
            throw new Exception('Failed to get new order');
        }
    }

    public function StatisticsByMonth()
    {
        try {

            $currentMonth = date('m');
            $currentYear = date('Y');

            $income = $this->order->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('total_money');
            $amount = $this->order->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
            $orders = $this->order->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->get();

            $interest = 0;
            $principal = 0;
            $sum = 0;

            foreach ($orders as $key => $value) {
                $sum += $value->total_money;
                foreach ($value->orderdetail as $key => $item) {
                    $principal += $item->product->price * $item->quantity;
                }
            }

            if ($sum == 0) {
                $moneyinterest = 0;
                $interest = 0;
            } else {
                $moneyinterest = $sum - $principal;
                $interest = $moneyinterest / $sum;
            }

            return [
                'income' => number_format($income, 0, ',', '.') . ' VND',
                'amount' => $amount,
                'moneyinterest' => number_format($moneyinterest, 0, ',', '.') . ' VND',
                'interest' => number_format($interest * 100, 1) . '%'
            ];
        } catch (Exception $e) {
            Log::error('Failed to calculate monthly income: ' . $e->getMessage());
            throw new Exception('Failed to calculate monthly income');
        }
    }

    public function StatisticsByYear()
    {
        try {
            $currentYear = date('Y');
            $income = $this->order->whereYear('created_at', $currentYear)->sum('total_money');
            $amount = $this->order->whereYear('created_at', $currentYear)->count();
            $orders = $this->order->whereYear('created_at', $currentYear)->get();

            $interest = 0;
            $principal = 0;
            $sum = 0;

            foreach ($orders as $key => $value) {
                $sum += $value->total_money;
                foreach ($value->orderdetail as $key => $item) {
                    $principal += $item->product->price * $item->quantity;
                }
            }

            if ($sum == 0) {
                $moneyinterest = 0;
                $interest = 0;
            } else {
                $moneyinterest = $sum - $principal;
                $interest = $moneyinterest / $sum;
            }

            return [
                'income' => number_format($income, 0, ',', '.') . ' VND',
                'amount' => $amount,
                'moneyinterest' => number_format($moneyinterest, 0, ',', '.') . ' VND',
                'interest' => number_format($interest * 100, 1) . '%'
            ];
        } catch (Exception $e) {
            Log::error('Failed to calculate yearly income: ' . $e->getMessage());
            throw new Exception('Failed to calculate yearly income');
        }
    }

    public function getStatistics($startDate = null, $endDate = null)
    {
        $query = Order::query();

        if ($startDate && $endDate) {
            try {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                \Log::error('Date parsing error: ' . $e->getMessage());
            }
        }

        // Tổng doanh số
        $totalRevenue = $query->where('status', 'completed')
            ->sum('total');

        // Tổng số đơn hàng
        $totalOrders = $query->count();

        // Đơn hàng gần đây (6 đơn mới nhất)
        $recentOrders = $query->with(['user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_code' => $order->order_code,
                    'order_name' => $order->order_name,
                    'customer_name' => $order->full_name,
                    'phone_number' => $order->phone_number,
                    'total' => $order->total,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s')
                ];
            });

        // Sản phẩm bán chạy (6 sản phẩm bán nhiều nhất)
        $bestSellingProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(quantity * price) as total_revenue'),
            DB::raw('COUNT(DISTINCT order_id) as total_orders')
        )
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with(['product:id,name,image,category_id,sale_price,discount_price,discount_start,discount_end,stock', 'product.category:id,name'])
            ->take(6)
            ->get()
            ->map(function ($item) {
                $product = $item->product;
                $product->price = isOnSale($product) ? $product->discount_price : $product->sale_price;
                $product->image = showImage($product->image);
                return [
                    'product' => $product,
                    'sold_quantity' => $item->total_quantity,
                    'total_revenue' => $item->total_revenue,
                    'total_orders' => $item->total_orders,
                    'average_quantity_per_order' => round($item->total_quantity / $item->total_orders, 1)
                ];
            });

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'recent_orders' => $recentOrders,
            'best_selling_products' => $bestSellingProducts,
        ];
    }

    public function getNewestProducts($startDate = null, $endDate = null)
    {
        $query = Product::query();

        if ($startDate && $endDate) {
            try {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                \Log::error('Date parsing error: ' . $e->getMessage());
            }
        }

        return $query->with(['brand', 'category'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($product) {
                $product->image = showImage($product->image);
                return $product;
            });
    }
}
