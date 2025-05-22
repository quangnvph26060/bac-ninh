<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // Xử lý khoảng ngày nếu có
        $start = null;
        $end = null;

        if ($request->filled('date_range')) {
            [$start, $end] = explode(' - ', $request->date_range);
            $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
            $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
        }

        // Hàm helper tạo query
        $baseQuery = function () use ($user, $start, $end) {
            $query = Order::where('user_id', $user->id);
            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }
            return $query;
        };

        $orderCounts = [
            'all' => (clone $baseQuery())->count(),

            'in_production' => (clone $baseQuery())
                ->where('status', 'in_production')->count(),

            'shipping' => (clone $baseQuery())
                ->where('status', 'completed_waiting_for_shipment')->count(),

            'shipped' => (clone $baseQuery())
                ->where('status', 'shipped')->count(),

            'cancelled' => (clone $baseQuery())
                ->where('status', 'cancelled')->count(),

            'unpaid' => (clone $baseQuery())
                ->where('payment_status', 'pending')->count(),
        ];

        if ($request->ajax()) {
            return response()->json($orderCounts);
        }

        return view('frontend.app.dashboard', compact('orderCounts'));
    }

}
