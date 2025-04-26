<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function coupons(Request $request)
    {

        $search = $request->search;

        $query = Coupon::with(['users' => function ($query) {
            $query->where('user_id', auth()->id());
        }]);

        if ($search) {
            $query->where('code', 'like', '%' . $search . '%'); // 'code' là mã đơn hàng
        }

        $coupons = $query->where('start_date', '<=', now())->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            $html = view('frontend.app.coupon._coupon_table', compact('coupons'))->render();
            return response()->json([
                'html' => $html,
            ]);
        }

        return view('frontend.app.coupon.index');
    }
}
