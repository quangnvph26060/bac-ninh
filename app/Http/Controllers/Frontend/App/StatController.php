<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth('web')->id();
        $walletId = auth()->user()->wallet->id ?? null;

        if (!$walletId) {
            $wallet = Wallet::create(['user_id' => $userId]);
            $walletId = $wallet->id;
        }

        $start = $request->start_date;
        $end = $request->end_date;

        // Tổng tiền nạp
        $totalTopup = WalletTransaction::where('wallet_id', $walletId)
            ->where('type', 'deposit')
            ->where('status', 'complete')
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->sum('amount');

        // DB::enableQueryLog();
        // Tổng chi tiêu
        $totalSpent = Order::where('user_id', $userId)
            ->where('payment_status', 'completed')
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->sum('total');

        // dd(DB::getQueryLog());

        // Tổng hoàn tiền
        $totalRefund = WalletTransaction::where('wallet_id', $walletId)
            ->where('type', 'withdraw')
            ->where('status', 'complete')
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->sum('amount');

        // Chi tiêu theo ngày
        $dailySpent = Order::where('user_id', $userId)
            ->where('payment_status', 'completed')
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // dd($dailySpent);

        // Top sản phẩm
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->when($start, fn($q) => $q->whereDate('orders.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('orders.created_at', '<=', $end))
            ->select('order_items.product_name', DB::raw('SUM(order_items.price * order_items.quantity) as spent'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('spent')
            ->limit(5)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'total_topup' => formatPrice($totalTopup) . ' USD',
                'total_spent' => formatPrice($totalSpent) . ' USD',
                'total_refund' => $totalRefund,
                'wallet_balance' => formatPrice(auth()->user()->wallet->balance) . ' USD' ?? 0,
                'daily_spent' => $dailySpent,
                'top_products' => $topProducts,
            ]);
        }

        return view('frontend.app.statistical.index');
    }
}
