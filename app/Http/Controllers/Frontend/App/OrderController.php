<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\City;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;
        $dateRange = $request->date_range;
        $perPage = $request->input('per_page', 10);

        $query = Order::query()->with(['user', 'orderItems']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('order_code', 'like', '%' . $search . '%'); // 'code' là mã đơn hàng
        }

        if ($dateRange) {
            [$start, $end] = explode(' - ', $dateRange);
            $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
            $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            $html = view('frontend.app.order.order-table', compact('orders'))->render();
            return response()->json([
                'html' => $html,
            ]);
        }

        $totalPendingOrders = Order::where('status', 'pending')->count();

        return view('frontend.app.order.index', compact('orders', 'totalPendingOrders'));
    }

    public function create(Request $request)
    {
        $perPage = $request->per_page ?? 10;
        $searchText = $request->search_text;

        $products = Product::query()
            ->with(['attributes', 'variants'])
            ->active()
            ->when($searchText, function ($query, $searchText) {
                $query->where('name', 'like', '%' . $searchText . '%');
            })
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('frontend.app.order._product_list', compact('products'))->render();
        }

        $shippingMethods = ShippingMethod::query()->latest()->get();

        $countries = Country::query()->orderBy('name', 'asc')->get();

        return view('frontend.app.order.create', compact('products', 'countries', 'shippingMethods'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'required|exists:coupons,code',
            'options' => 'required|array',
            'options.*.productId' => 'required|exists:products,id',
            'options.*.variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $coupon = Coupon::query()->with('products')->where('code', $request->coupon)->first();

        if (!$this->isCouponValid($coupon)) {
            return errorResponse("Mã giảm giá đã hết hiệu lực hoặc không tồn tại!", true);
        }

        // dd($request->toArray());

        $shippingFee = $request->shipping;
        $items = $request->options;

        $subTotal = 0;
        $productDetails = [];

        foreach ($items as $item) {
            $productId = $item['productId'];
            $qty = $item['qty'];
            $variantId = $item['variant_id'] ?? null;

            if ($variantId) {
                $variant = ProductVariant::find($variantId);

                $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;
                $productDetails[] = [
                    'id' => $productId,
                    'variant_id' => $variantId,
                    'price' => $price,
                    'qty' => $qty,
                    'total' => $price * $qty,
                ];
                $subTotal += $price * $qty;
            } else {
                $product = Product::find($productId);
                $price = isOnSale($product) ? $product->discount_price : $product->sale_price;
                $productDetails[] = [
                    'id' => $productId,
                    'variant_id' => null,
                    'price' => $price,
                    'qty' => $qty,
                    'total' => $price * $qty,
                ];
                $subTotal += $price * $qty;
            }
        }

        // Tính giảm giá nếu có coupon
        $discountAmount = 0;

        if ($coupon->type === 'order') {

            $discountAmount = $coupon->value;

            // Áp dụng giới hạn giảm nếu có
            if ($coupon->max_discount && $discountAmount > $coupon->max_discount) {
                $discountAmount = $coupon->max_discount;
            }
        }

        if ($coupon->type === 'product') {
            $applicableIds = $coupon->products->pluck('id')->toArray(); // Danh sách productId

            foreach ($productDetails as $item) {
                // Nếu coupon áp dụng cho tất cả hoặc trùng id
                if (empty($applicableIds) || in_array($item['id'], $applicableIds)) {
                    // Tính giảm giá 5% cho từng sản phẩm
                    $discountAmount += ($coupon->value / 100) * $item['total']; // Áp dụng phần trăm trên tổng giá trị từng sản phẩm
                }
            }

            // Áp dụng giới hạn giảm nếu có
            if ($coupon->max_discount && $discountAmount > $coupon->max_discount) {
                $discountAmount = $coupon->max_discount;
            }
        }

        $grandTotal = max(0, $subTotal - $discountAmount + $shippingFee);

        return response()->json([
            'discount' => $discountAmount,
            'grand_total' => $grandTotal,
        ], 200);
    }

    protected function isCouponValid($coupon): bool
    {
        $now = Carbon::now();

        if ($coupon->status == 2) return false;

        // Nếu có start_date và không có end_date => kiểm tra now >= start_date
        if ($coupon->start_date && !$coupon->end_date) {
            return $now->greaterThanOrEqualTo($coupon->start_date);
        }

        // Nếu có cả start_date và end_date => kiểm tra now nằm trong khoảng
        if ($coupon->start_date && $coupon->end_date) {
            $start = $coupon->start_date;
            $end = $coupon->end_date;
            return $now->between($start, $end); // inclusive mặc định
        }

        // Nếu không có start_date => không hợp lệ
        return false;
    }

    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get(['id', 'name']);
        if (!$country = Country::query()->with('shippingMethods')->find($country_id)) {
            $msg = "Quốc gia không hợp lệ!";
        }

        if ($country->shippingMethods->isEmpty()) {
            $msg = "Quốc gia {$country->name} không được hỗ trợ vận chuyển.";
        }

        $shippingMethods = $country->shippingMethods->map(fn($method) => [
            'id'   => $method->id,
            'name' => $method->name,
            'fee'  => number_format($method->pivot->fee, 0),
        ]);

        return response()->json([
            'states' => $states,
            'msg' => $msg ?? '',
            'shipping_methods' => $shippingMethods
        ]);
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->get(['id', 'name']);
        return response()->json([
            'cities' => $cities
        ]);
    }

    public function getProducts(Request $request)
    {
        $ids = $request->input('product_ids');

        $products = Product::query()
            ->select('id', 'image', 'name', 'sku',  'type', 'sale_price', 'discount_price', 'stock', 'stock_status')
            ->whereIn('id', $ids)
            ->get();

        $result = [];

        foreach ($products as $product) {
            // Nếu sản phẩm có type là variant, lấy attributes
            if ($product->type === 'variant') {
                $attributesFormatted = [];

                foreach ($product->attributes as $attribute) {
                    $valueIds = json_decode($attribute->pivot->attribute_values_ids, true);

                    $values = AttributeValue::whereIn('id', $valueIds)->pluck('value', 'id')->toArray();

                    $attributesFormatted[] = [
                        'attribute_name' => $attribute->name,
                        'values' => $values
                    ];
                }

                $result[] = [
                    'id' => $product->id,
                    'image' => showImage($product->image),
                    'name' => $product->name,
                    'attributes' => $attributesFormatted,
                    'type' => $product->type,
                ];
            } else {
                // Nếu sản phẩm có type là simple, trả về giá sản phẩm và không có attributes
                $price = isOnSale($product) ? $product->discount_price : $product->sale_price;

                $result[] = [
                    'id' => $product->id,
                    'image' => showImage($product->image),
                    'name' => $product->name,
                    'attributes' => [],
                    'price' => number_format($price, 0, ',', ''),
                    'type' => $product->type,
                    'sku' => $product->sku
                ];
            }
        }

        return response()->json($result);
    }


    public function getVariantPrice(Request $request)
    {
        $productId = $request->input('product_id');
        $valueIds = $request->input('value_ids'); // array, có thể là null nếu là simple

        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Nếu là sản phẩm đơn giản
        if ($product->type === 'simple') {
            if ($product->stock <= 0 || $product->stock_status == 'out_of_stock') {
                return response()->json(['message' => 'Số lượng trong kho không đủ!'], 400);
            }

            $price = isOnSale($product) ? $product->discount_price : $product->sale_price;

            return response()->json([
                'price' => number_format($price, 0, ',', ''),
                'variant_id' => null // Không có variant_id cho sản phẩm đơn giản
            ]);
        }

        // Nếu là sản phẩm có biến thể
        $variant = ProductVariant::where('product_id', $productId)
            ->whereHas('attributeValues', function ($q) use ($valueIds) {
                $q->whereIn('attribute_value_id', $valueIds);
            }, '=', count($valueIds))
            ->first();

        if (!$variant) {
            return response()->json(['message' => 'Không tìm thấy biến thể phù hợp'], 404);
        }

        if ($variant->stock <= 0 || $variant->stock_status == 'out_of_stock') {
            return response()->json(['message' => 'Số lượng trong kho không đủ!'], 400);
        }

        $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;

        return response()->json([
            'price' => number_format($price, 0, ',', ''),
            'variant_id' => $variant->id,
            'sku' =>  $variant->sku
        ]);
    }


    public function checkStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'nullable|exists:product_variants,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $qty = (int) $request->quantity;

        // Lấy sản phẩm từ DB
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        // Kiểm tra nếu là sản phẩm dạng variant
        if ($product->type === 'variant' && $variantId) {
            $variant = ProductVariant::find($variantId);

            if (!$variant || $variant->product_id != $productId) {
                return response()->json(['message' => 'Không tìm thấy biến thể phù hợp'], 404);
            }

            $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;

            // Kiểm tra số lượng kho của biến thể
            if ($variant->stock < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng trong kho không đủ!',
                    'totalPrice' => number_format($price, 0, ',', '')
                ], 400);
            }

            return response()->json([
                'success' => true,
                'stock' => $variant->stock,
                'totalPrice' => $price * $qty
            ]);
        } else {
            // Sản phẩm không phải là variant, lấy giá và kiểm tra kho của sản phẩm chính
            $price = isOnSale($product) ? $product->discount_price : $product->sale_price;

            if ($product->stock < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng trong kho không đủ!',
                    'totalPrice' => number_format($price, 0, ',', '')
                ], 400);
            }

            return response()->json([
                'success' => true,
                'stock' => $product->stock,
                'totalPrice' => $price * $qty
            ]);
        }
    }
}
