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
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;
        $dateRange = $request->date_range;
        $perPage = $request->input('per_page', 10);

        $query = Order::query()->where('user_id', auth()->id())->with(['user', 'orderItems']);

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

        $orders = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            $html = view('frontend.app.order.order-table', compact('orders'))->render();
            return response()->json([
                'html' => $html,
            ]);
        }

        $totalPendingOrders = Order::where(['status' => 'pending', 'user_id' => auth()->id()])->count();

        return view('frontend.app.order.index', compact('orders', 'totalPendingOrders'));
    }

    public function show(string $code)
    {
        $order = Order::query()->where('order_code', $code)->with(['orderItems.productVariant.attributeValues'])->firstOrFail();
        return view('frontend.app.order.show', compact('order'));
    }

    public function payment(Request $request)
    {
        $credentials = $request->validate([
            'order_code' => 'required|exists:orders,order_code',
        ]);

        $order = Order::query()->where(['order_code' => $credentials['order_code'], 'user_id' => auth()->id()])->first();

        if (!$order) {
            return errorResponse("Đơn hàng không tồn tại!", true);
        }

        if ($order->payment_status === "completed" || $order->status !== "draft") {
            return errorResponse("Đơn hàng đã được thanh toán!", true);
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0]
        );

        if ($order->total > $wallet->balance) {
            return errorResponse("Số dư tài khoản không đủ, vui lòng nạp tiền để tiếp tục!", true);
        }

        try {
            DB::beginTransaction();
            $order->payment_status = "completed";
            $order->status = "pending";
            $order->payment_method = "bank_transfer";

            $order->save();

            $this->paymentViaWallet($order, $wallet);

            DB::commit();

            return successResponse(
                "Thanh toán đơn hàng thành công.",
                ['amount' => formatPrice($wallet->balance)],
                200,
                true
            );
        } catch (\Exception $e) {
            DB::rollBack();
            logger("error: " . $e->getMessage());
            return errorResponse('Đã có lỗi xảy ra trong quá trình thanh toán!', 400);
        }
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

        $coupon = Coupon::query()->with(['products', 'users'])->where('code', $request->coupon)->first();

        if (!$this->isCouponValid($coupon)) {
            return errorResponse("Mã giảm giá đã hết hiệu lực hoặc không tồn tại!", true);
        }

        if ($coupon->usage_limit > 0 && $coupon->users()->count() >= $coupon->usage_limit) {
            return errorResponse("Mã giảm giá đã hết hiệu lực, vui lòng chọn mã khác!", true, 400);
        }


        if ($coupon->users()->where('user_id', auth()->guard('web')->id())->count() >= $coupon->usage_per_user)  return errorResponse("Bạn đã hết lượt sử dụng mã giảm giá này rồi!", true, 400);

        $shippingFee = $request->shipping;
        $items = $request->options;

        $productDetails = $this->getProductDetails($items);
        $subTotal = $this->calculateSubTotal($productDetails);

        // Tính giảm giá nếu có coupon
        $discountAmount = $this->calculateDiscount($coupon, $productDetails, $subTotal);

        if ($discountAmount == false) {
            return errorResponse("Các sản phẩm không đủ điều kiện áp dụng mã giảm giá này.", true);
        }

        $grandTotal = $this->calculateGrandTotal($subTotal, $discountAmount, $shippingFee);

        return response()->json([
            'subTotal' => $subTotal,
            'discount' => $discountAmount,
            'grand_total' => $grandTotal,
        ], 200);
    }

    // Hàm lấy thông tin sản phẩm chi tiết
    private function getProductDetails($items)
    {
        $productDetails = [];

        foreach ($items as $item) {
            $productId = $item['productId'];
            $qty = $item['qty'];
            $variantId = $item['variant_id'] ?? null;

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                $product = $variant->product; // Lấy sản phẩm từ biến thể
                $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;
                $originalPrice = $variant->sale_price; // Giá gốc
                $productDetails[] = [
                    'name' => $product->name,
                    'id' => $productId,
                    'variant_id' => $variantId,
                    'price' => $price,
                    'original_price' => $originalPrice,
                    'qty' => $qty,
                    'total' => $price * $qty,
                    'image' =>  $product->image, // Ảnh sản phẩm (ưu tiên ảnh của biến thể)
                ];
            } else {
                $product = Product::find($productId);
                $price = isOnSale($product) ? $product->discount_price : $product->sale_price;
                $originalPrice = $product->sale_price; // Giá gốc

                $productDetails[] = [
                    'id' => $productId,
                    'name' => $product->name,
                    'variant_id' => null,
                    'price' => $price,
                    'original_price' => $originalPrice, // Giá gốc
                    'qty' => $qty,
                    'total' => $price * $qty,
                    'image' => $product->image, // Ảnh sản phẩm
                ];
            }
        }

        return $productDetails;
    }

    // Hàm kiểm tra tính hợp lệ của coupon
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

    // Hàm tính tổng tiền của các sản phẩm (subTotal)
    private function calculateSubTotal($productDetails)
    {
        $subTotal = 0;
        foreach ($productDetails as $item) {
            $subTotal += $item['total'];
        }
        return $subTotal;
    }

    // Hàm tính giảm giá dựa trên coupon
    // Hàm tính giảm giá dựa trên coupon
    private function calculateDiscount($coupon, $productDetails, $subTotal)
    {
        $discountAmount = 0;

        if ($coupon === null) return $discountAmount;

        if ($coupon->type === 'order') {
            $discountAmount = $coupon->value;

            // Áp dụng giới hạn giảm nếu có
            if ($coupon->max_discount > 0 && $discountAmount > $coupon->max_discount) {
                $discountAmount = $coupon->max_discount;
            }
        }

        if ($coupon->type === 'product') {
            $applicableIds = $coupon->products->pluck('id')->toArray(); // Danh sách productId có thể áp dụng coupon

            // Kiểm tra nếu có sản phẩm nào không hợp lệ
            $invalidProducts = [];
            foreach ($productDetails as $item) {
                if (!in_array($item['id'], $applicableIds)) {
                    $invalidProducts[] = $item['id']; // Lưu lại các sản phẩm không hợp lệ
                }
            }

            // Nếu có sản phẩm không hợp lệ, trả về lỗi
            if (!empty($invalidProducts) && !empty($applicableIds)) {
                return false;
            }

            foreach ($productDetails as $item) {
                // Tính giảm giá 5% cho từng sản phẩm hợp lệ
                $discountAmount += ($coupon->value / 100) * $item['total']; // Áp dụng phần trăm trên tổng giá trị từng sản phẩm
            }

            // Áp dụng giới hạn giảm nếu có
            if ($coupon->max_discount > 0 && $discountAmount > $coupon->max_discount) {
                $discountAmount = $coupon->max_discount;
            }
        }

        return $discountAmount;
    }

    // Hàm tính tổng tiền đơn hàng (grandTotal)
    private function calculateGrandTotal($subTotal, $discountAmount, $shippingFee)
    {
        logger("$subTotal - $discountAmount + $shippingFee");
        return max(0, $subTotal - $discountAmount + $shippingFee);
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
                    'price' => formatPrice($price),
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

    public function storeOrder(Request $request)
    {
        $request->validate(
            [
                'products' => 'required|array|min:1',
                'products.*.productId' => 'required|integer|exists:products,id',
                'products.*.qty' => 'required|integer|min:1',
                'products.*.variant_id' => 'nullable|integer|exists:product_variants,id',

                'orderInfo' => 'required|array',
                'orderInfo.coupon' => 'nullable|string|max:255|exists:coupons,code',
                'orderInfo.paymentMethod' => 'required|string|in:later,wallet', // tùy nếu bạn có 2 phương thức này
                'orderInfo.shipping_method_id' => 'required|integer|exists:shipping_methods,id',
                'orderInfo.first_name' => 'required|string|max:255',
                'orderInfo.last_name' => 'required|string|max:255',
                'orderInfo.email' => 'required|email|max:255',
                'orderInfo.phone_number' => 'required|string|max:20',
                'orderInfo.country_id' => 'required|integer|exists:countries,id',
                'orderInfo.state_id' => 'required|integer|exists:states,id',
                'orderInfo.city_id' => 'required|integer|exists:cities,id',
                'orderInfo.zip_code' => 'required|string|max:20',
                'orderInfo.shipping_address' => 'required|string|max:500',
                'orderInfo.note' => 'nullable|string|max:255',
                'orderInfo.orderName' => 'required|max:255|unique:orders,order_name',
            ],
            __('request.messages'),
            [
                'products' => 'sản phẩm',
                'products.*.productId' => 'ID sản phẩm',
                'products.*.qty' => 'số lượng',
                'products.*.variant_id' => 'biến thể sản phẩm',

                'orderInfo.coupon' => 'mã giảm giá',
                'orderInfo.paymentMethod' => 'phương thức thanh toán',
                'orderInfo.shipping_method_id' => 'phương thức giao hàng',
                'orderInfo.first_name' => 'họ',
                'orderInfo.last_name' => 'tên',
                'orderInfo.email' => 'email',
                'orderInfo.phone_number' => 'số điện thoại',
                'orderInfo.country_id' => 'quốc gia',
                'orderInfo.state_id' => 'tỉnh/thành phố',
                'orderInfo.city_id' => 'quận/huyện',
                'orderInfo.zip_code' => 'mã bưu điện',
                'orderInfo.shipping_address' => 'địa chỉ giao hàng',
                'orderInfo.orderName' => 'Tên đơn hàng',
                'orderInfo.note' => 'Ghi chú'
            ]
        );

        try {
            DB::beginTransaction();

            // Lấy thông tin vận chuyển
            $shippingDetails = $this->getShippingDetails($request['orderInfo']);
            extract($shippingDetails);

            // Kiểm tra mã giảm giá nếu có
            $coupon = $this->checkCoupon($request['orderInfo']['coupon'] ?? null);

            if ($coupon === false) return errorResponse("Mã giảm giá đã hết hiệu lực hoặc không tồn tại!", true);

            // Lấy chi tiết sản phẩm và tính toán tổng đơn hàng
            $productDetails = $this->getProductDetails($request['products']);

            $subTotal = $this->calculateSubTotal($productDetails);
            $totals = $this->calculateOrderTotals($productDetails, $coupon, $subTotal, $shippingFee);
            if (isset($totals['success']) && $totals['success'] === false) return errorResponse($totals['message'], true, $totals['code']);
            extract($totals);

            // Tạo đơn hàng và lưu các sản phẩm
            $order = $this->storeOrderDetails($request['orderInfo'], $grandTotal, $discountAmount, $shippingFee, $shippingAddress);
            $this->storeOrderItems($order, $productDetails);
            $this->couponUser($coupon, $order);

            if ($request['orderInfo']['paymentMethod'] === "wallet") {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => auth()->id()],
                    ['balance' => 0]
                );

                if ($wallet->balance < $grandTotal) return errorResponse("Số dư tài khoản không đủ, vui lòng nạp tiền để tiếp tục thanh toán", true, 400);

                $this->paymentViaWallet($order, $wallet);
            }

            DB::commit();

            // OrderCreated.php (Event)
            // UpdateStockOnOrderCreated (Listener)

            return handleResponse('Tạo đơn hàng thành công.', 'success', 201);
        } catch (\Exception $e) {
            logger("message: " . $e->getMessage() . "line: " . $e->getLine());

            DB::rollBack();
            return errorResponse('Đã có lỗi xảy ra trong quá trình tạo đơn hàng, vui lòng quay lại sau!', true);
        }
    }

    private function paymentViaWallet($order, $wallet, string $type = 'withdraw')
    {
        $amount = $order->total;
        $note = ($type === "withdraw" ? "ORDER PAYMENT" : "REFUND THE ORDER") . " #{$order->order_code}";

        $balanceBefore = $wallet->balance;

        if ($type === 'withdraw') {
            $wallet->decrement('balance', $amount);
        } elseif ($type === 'deposit') {
            $wallet->increment('balance', $amount);
        } else {
            return errorResponse("Invalid transaction type!", true, 400);
        }

        $balanceAfter = $wallet->fresh()->balance;

        $wallet->transactions()->create([
            'code' => generateTransactionCode(),
            'amount' => $amount,
            'note' => $note,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'type' => $type
        ]);
    }

    public function couponUser($coupon, $order)
    {
        if (!empty($coupon)) {
            $user = User::query()->findOrFail(auth()->guard('web')->id());

            DB::table('coupon_user_usages')->insert([
                'user_id'    => $user->id,
                'coupon_id'  => $coupon->id,
                'order_id' => $order->id,
                'usage_time' => now(),
            ]);
        }
    }

    private function checkCoupon($coupon)
    {
        if (!empty($coupon)) {
            $coupon = Coupon::query()->with(['products', 'users'])->where('code', $coupon)->lockForUpdate()->first();

            if ($this->isCouponValid($coupon)) return $coupon;
            return null;
        }

        return null;
    }

    private function getShippingDetails($orderInfo)
    {
        $country = Country::findOrFail($orderInfo['country_id']);
        $state = State::query()->find($orderInfo['state_id'])->name;
        $city = City::query()->find($orderInfo['city_id'])->name;

        $shippingMethod = $country->shippingMethods()
            ->where('shipping_method_id', $orderInfo['shipping_method_id'])
            ->first();

        $shippingFee = $shippingMethod ? $shippingMethod->pivot->fee : 0;

        $shippingAddress = "{$orderInfo['shipping_address']}, {$city}, {$state}, {$country->name}";

        return compact('shippingAddress', 'shippingFee');
    }

    private function calculateOrderTotals($productDetails, $coupon, $subTotal, $shippingFee)
    {
        if ($coupon && $coupon->min_order_value >= $subTotal)  return errorResponse("Giá trị đơn hàng chưa đáp ứng điều kiện!", false, 400);

        $discountAmount = $this->calculateDiscount($coupon ?? null, $productDetails, $subTotal);

        if ($discountAmount === false) return errorResponse("Các sản phẩm không đủ điều kiện áp dụng mã giảm giá này!", false, 400);

        $grandTotal = $this->calculateGrandTotal($subTotal, $discountAmount, $shippingFee);

        return compact('subTotal', 'discountAmount', 'grandTotal');
    }

    private function storeOrderDetails($orderInfo, $grandTotal, $discountAmount, $shippingFee, $shippingAddress)
    {
        return Order::create(
            [
                'user_id' => auth()->id(),
                'full_name' => $orderInfo['first_name'] . ' ' . $orderInfo['last_name'],
                'email' => $orderInfo['email'],
                'zip_code' => $orderInfo['zip_code'],
                'order_code' => generateOrderCode(),
                'order_name' => $orderInfo['orderName'],
                'status' => $orderInfo['paymentMethod'] !== 'later' ? 'pending' : 'draft',
                'payment_status' => $orderInfo['paymentMethod'] !== 'later' ? 'completed' : 'pending',
                'payment_method' => $orderInfo['paymentMethod'] !== 'later' ? 'bank_transfer' : null,
                'phone_number' => $orderInfo['phone_number'],
                'shipping_address' => $shippingAddress,
                'note' => $orderInfo['note'],
                'total' => $grandTotal,
                'discount' => $discountAmount,
                'shipping_fee' => $shippingFee
            ]
        );
    }

    private function storeOrderItems($order, $productDetails)
    {
        foreach ($productDetails as $product) {
            $order->orderItems()->create([
                'product_id' => $product['id'],
                'product_variant_id' => $product['variant_id'] ?? null,
                'product_name' => $product['name'],
                'quantity' => $product['qty'],
                'price' => $product['price'],
                'original_price' => $product['original_price'],
                'image' => $product['image'],
            ]);
        }
    }

    public function orderCancel(Request $request)
    {
        $credentials = $request->validate([
            'code' => 'required|exists:orders,order_code',
            'reason' => 'required|string|max:400'
        ]);

        $order = Order::query()->where('order_code', $credentials['code'])->firstOrFail();

        if ($order->status !== "pending") return errorResponse("Your order cannot be cancelled.", true, 400);

        $order->reason = $credentials['reason'];
        $order->status = "cancelled";
        $order->payment_status = "refunded";
        $order->save();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0]
        );

        $this->paymentViaWallet($order, $wallet, 'deposit');

        return successResponse("Hủy đơn hàng thành công.", ['wallet' => formatPrice($wallet->balance)], 200, true);
    }
}
