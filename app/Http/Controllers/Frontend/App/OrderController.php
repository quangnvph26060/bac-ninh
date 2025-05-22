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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $payment_status = $request->payment_status;
        $search = $request->search;
        $dateRange = $request->date_range;
        $perPage = $request->input('per_page', 10);

        $query = Order::query()->where('user_id', auth()->id())->with(['user', 'orderItems']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($payment_status) {
            $query->where('payment_status', $payment_status);
        }

        if ($search) {
            $query->where('order_code', 'like', '%' . $search . '%');
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

        return view('frontend.app.order.index', compact('orders'));
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
            return errorResponse("Order not found!", true);
        }

        if ($order->payment_status === "completed" || $order->status !== "draft") {
            return errorResponse("Order has already been paid!", true);
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0]
        );

        if ($order->total > $wallet->balance) {
            return errorResponse("Insufficient balance, please top up to continue!", true);
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
                "Order payment successful.",
                ['amount' => formatPrice($wallet->balance)],
                200,
                true
            );
        } catch (\Exception $e) {
            DB::rollBack();
            logger("error: " . $e->getMessage());
            return errorResponse('An error occurred during payment!', 400);
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

        // $shippingMethods = ShippingMethod::query()->latest()->get();
        $countries = Country::query()->orderBy('name', 'asc')->get();

        return view('frontend.app.order.create', compact('products', 'countries'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon' => 'required|exists:coupons,code',
            'options' => 'required|array',
            'options.*.productId' => 'required|exists:products,id',
            'options.*.variant_id' => 'nullable|exists:product_variants,id',
            'shipping' => 'required|string|in:standard_shipping,express_shipping,international_shipping',
        ]);

        $coupon = Coupon::query()->with(['products', 'users'])->where('code', $request->coupon)->first();

        if (!$this->isCouponValid($coupon)) {
            return errorResponse("Coupon has expired or does not exist!", true);
        }

        if ($coupon->usage_limit > 0 && $coupon->users()->count() >= $coupon->usage_limit) {
            return errorResponse("Coupon has expired, please choose another one!", true, 400);
        }

        if ($coupon->users()->where('user_id', auth()->guard('web')->id())->count() >= $coupon->usage_per_user) {
            return errorResponse("You have reached the maximum usage limit for this coupon!", true, 400);
        }

        $shippingFee = 0;
        $shippingMethod = $request->shipping;
        $items = $request->options;

        $this->calculateShippingFee($shippingMethod, $items, $shippingFee);

        $productDetails = $this->getProductDetails($items);
        $subTotal = $this->calculateSubTotal($productDetails);

        if ($coupon && $coupon->min_order_value >= $subTotal)
            return errorResponse("Giá trị đơn hàng chưa đáp ứng điều kiện mã giảm giá!", true, 400);

        $discountAmount = $this->calculateDiscount($coupon, $productDetails, $subTotal);

        if ($discountAmount === false) {
            return errorResponse("Products do not meet the coupon requirements.", true);
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
            $modelImage = $item['model_image'] ?? null;
            $designImage = $item['design_image'] ?? null;

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
                    'image' => $product->image, // Ảnh sản phẩm (ưu tiên ảnh của biến thể)
                    'model_image' => $modelImage,
                    'design_image' => $designImage,
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
                    'model_image' => $modelImage,
                    'design_image' => $designImage,
                ];
            }
        }

        return $productDetails;
    }

    // Hàm kiểm tra tính hợp lệ của coupon
    protected function isCouponValid($coupon): bool
    {
        $now = Carbon::now();

        if ($coupon->status == 2)
            return false;

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

        if ($coupon === null)
            return $discountAmount;

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
        return max(0, $subTotal - $discountAmount + $shippingFee);
    }

    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get(['id', 'name']);
        if (!$country = Country::query()->with('shippingMethods')->find($country_id)) {
            $msg = "Invalid country!";
        }

        if ($country->shippingMethods->isEmpty()) {
            $msg = "Shipping is not available for {$country->name}.";
        }

        $shippingMethods = $country->shippingMethods->map(fn($method) => [
            'id' => $method->id,
            'name' => $method->name,
            'fee' => number_format($method->pivot->fee, 0),
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
            ->select('id', 'image', 'name', 'sku', 'type', 'sale_price', 'discount_price', 'discount_start', 'discount_end', 'stock', 'stock_status', 'design_width', 'design_height', 'design_ppi', 'design_format')
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
                    'design_width' => $product->design_width,
                    'design_height' => $product->design_height,
                    'design_ppi' => $product->design_ppi,
                    'design_format' => $product->design_format,
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
                    'sku' => $product->sku,
                    'design_width' => $product->design_width,
                    'design_height' => $product->design_height,
                    'design_ppi' => $product->design_ppi,
                    'design_format' => $product->design_format,
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
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Nếu là sản phẩm đơn giản
        if ($product->type === 'simple') {
            if ($product->stock <= 0 || $product->stock_status == 'out_of_stock') {
                return response()->json(['message' => 'Insufficient stock!'], 400);
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
            return response()->json(['message' => 'Variant not found'], 404);
        }

        if ($variant->stock <= 0 || $variant->stock_status == 'out_of_stock') {
            return response()->json(['message' => 'Insufficient stock!'], 400);
        }

        $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;

        return response()->json([
            'price' => number_format($price, 0, ',', ''),
            'variant_id' => $variant->id,
            'sku' => $variant->sku
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
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Kiểm tra nếu là sản phẩm dạng variant
        if ($product->type === 'variant' && $variantId) {
            $variant = ProductVariant::find($variantId);

            if (!$variant || $variant->product_id != $productId) {
                return response()->json(['message' => 'Variant not found'], 404);
            }

            $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;

            // Kiểm tra số lượng kho của biến thể
            if ($variant->stock < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock!',
                    'totalPrice' => formatPrice($price)
                ], 400);
            }

            return response()->json([
                'success' => true,
                'stock' => $variant->stock,
                'totalPrice' => formatPrice($price * $qty)
            ]);
        } else {
            // Sản phẩm không phải là variant, lấy giá và kiểm tra kho của sản phẩm chính
            $price = isOnSale($product) ? $product->discount_price : $product->sale_price;

            if ($product->stock < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock!',
                    'totalPrice' => formatPrice($price)
                ], 400);
            }

            return response()->json([
                'success' => true,
                'stock' => $product->stock,
                'totalPrice' => formatPrice($price * $qty)
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
                'orderInfo.paymentMethod' => 'required|string|in:later,wallet',
                'orderInfo.shipping_method' => 'required|string|in:standard_shipping,express_shipping,international_shipping',
                'orderInfo.first_name' => 'required|string|max:255',
                'orderInfo.last_name' => 'required|string|max:255',
                'orderInfo.email' => 'required|email|max:255',
                'orderInfo.phone_number' => 'nullable|string|max:20',
                'orderInfo.country' => 'required|string|max:255',
                'orderInfo.state' => 'required|string|max:255',
                'orderInfo.city' => 'required|string|max:255',
                'orderInfo.zip_code' => 'required|string|max:20',
                'orderInfo.shipping_address' => 'required|string|max:500',
                'orderInfo.note' => 'nullable|string|max:255',
                'orderInfo.orderName' => 'required|max:255|unique:orders,order_name',
            ],
            __('request.messages'),
            [
                'products' => 'products',
                'products.*.productId' => 'product ID',
                'products.*.qty' => 'quantity',
                'products.*.variant_id' => 'product variant',

                'orderInfo.coupon' => 'coupon',
                'orderInfo.paymentMethod' => 'payment method',
                'orderInfo.shipping_method' => 'shipping method',
                'orderInfo.first_name' => 'first name',
                'orderInfo.last_name' => 'last name',
                'orderInfo.email' => 'email',
                'orderInfo.phone_number' => 'phone number',
                'orderInfo.country' => 'country',
                'orderInfo.state' => 'state',
                'orderInfo.city' => 'city',
                'orderInfo.zip_code' => 'zip code',
                'orderInfo.shipping_address' => 'shipping address',
                'orderInfo.orderName' => 'Order Name',
                'orderInfo.note' => 'Note'
            ]
        );

        try {
            DB::beginTransaction();

            // Lấy thông tin vận chuyển
            $shippingAddress = $this->getShippingDetails($request['orderInfo']);

            $shippingFee = 0;
            $shippingMethod = $request['orderInfo']['shipping_method'];

            $this->calculateShippingFee($shippingMethod, $request['products'], $shippingFee);

            // Kiểm tra mã giảm giá nếu có
            $coupon = $this->checkCoupon($request['orderInfo']['coupon'] ?? null);

            if ($coupon === false)
                return errorResponse("Coupon has expired or does not exist!", true);

            // Lấy chi tiết sản phẩm và tính toán tổng đơn hàng
            $productDetails = $this->getProductDetails($request['products']);

            $subTotal = $this->calculateSubTotal($productDetails);
            $totals = $this->calculateOrderTotals($productDetails, $coupon, $subTotal, $shippingFee);
            if (isset($totals['success']) && $totals['success'] === false)
                return errorResponse($totals['message'], true, $totals['code']);
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

                if ($wallet->balance < $grandTotal)
                    return errorResponse("Insufficient balance, please top up to continue payment", true, 400);

                $this->paymentViaWallet($order, $wallet);
            }

            DB::commit();

            // OrderCreated.php (Event)
            // UpdateStockOnOrderCreated (Listener)

            return handleResponse('Order created successfully.', 'success', 201);
        } catch (\Exception $e) {
            logger("message: " . $e->getMessage() . "line: " . $e->getLine());

            DB::rollBack();
            return errorResponse('An error occurred while creating the order, please try again later!', true);
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
                'user_id' => $user->id,
                'coupon_id' => $coupon->id,
                'order_id' => $order->id,
                'usage_time' => now(),
            ]);
        }
    }

    private function checkCoupon($coupon)
    {
        if (!empty($coupon)) {
            $coupon = Coupon::query()->with(['products', 'users'])->where('code', $coupon)->lockForUpdate()->first();

            if ($this->isCouponValid($coupon))
                return $coupon;
            return null;
        }

        return null;
    }

    private function getShippingDetails($orderInfo)
    {
        return "{$orderInfo['shipping_address']}, {$orderInfo['city']}, {$orderInfo['state']}, {$orderInfo['country']}";
    }

    private function calculateOrderTotals($productDetails, $coupon, $subTotal, $shippingFee)
    {

        $discountAmount = $this->calculateDiscount($coupon ?? null, $productDetails, $subTotal);

        if ($discountAmount === false)
            return errorResponse("Các sản phẩm không đủ điều kiện áp dụng mã giảm giá này!", false, 400);

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
                'status' => 'pending',
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
        // Mảng để lưu lại các hình ảnh đã upload thành công
        $uploadedImages = [];

        try {
            foreach ($productDetails as $index => $product) {
                // Lấy đường dẫn ảnh cho từng sản phẩm
                $modelImagePath = null;
                $designImagePath = null;

                if (isset($product['model_image']) && $product['model_image'] instanceof \Illuminate\Http\UploadedFile) {
                    $modelImagePath = uploadImages("products.$index.model_image", 'model_images', false, 150, 150, false);
                    $uploadedImages[] = $modelImagePath;
                }

                if (isset($product['design_image']) && $product['design_image'] instanceof \Illuminate\Http\UploadedFile) {
                    $designImagePath = uploadImages("products.$index.design_image", 'design_images', false, 150, 150, false);
                    $uploadedImages[] = $designImagePath;
                }

                $order->orderItems()->create([
                    'product_id' => $product['id'],
                    'product_variant_id' => $product['variant_id'] ?? null,
                    'product_name' => $product['name'],
                    'quantity' => $product['qty'],
                    'price' => $product['price'],
                    'original_price' => $product['original_price'],
                    'image' => $product['image'],
                    'model_image' => $modelImagePath,
                    'design_image' => $designImagePath,
                ]);
            }
        } catch (\Exception $e) {
            logger("Lỗi xảy ra khi lưu sản phẩm: " . $e->getMessage());

            // Nếu lỗi xảy ra, xóa tất cả ảnh đã upload
            foreach ($uploadedImages as $path) {
                deleteImage($path);
            }

            throw new \Exception("Đã có lỗi xảy ra khi lưu sản phẩm. Ảnh đã được rollback.");
        }
    }


    public function orderCancel(Request $request)
    {
        $credentials = $request->validate([
            'code' => 'required|exists:orders,order_code',
            'reason' => 'required|string|max:400'
        ]);

        $order = Order::query()->where('order_code', $credentials['code'])->firstOrFail();

        if ($order->status !== "pending")
            return errorResponse("Your order cannot be cancelled.", true, 400);

        $order->reason = $credentials['reason'];
        $order->status = "cancelled";
        $order->payment_status = "refunded";
        $order->save();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0]
        );

        $this->paymentViaWallet($order, $wallet, 'deposit');

        return successResponse("Order cancelled successfully.", ['wallet' => formatPrice($wallet->balance)], 200, true);
    }

    public function getShippingFee(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|in:standard_shipping,express_shipping,international_shipping',
            'products' => 'required|array|min:1',
        ]);

        $sum = 0;
        $shippingMethod = $request->shipping_method;

        // Gọi hàm tính phí vận chuyển
        $error = $this->calculateShippingFee($shippingMethod, $request->products, $sum);

        // Nếu có lỗi, trả về JSON error
        if ($error) {
            return response()->json([
                'success' => false,
                'message' => $error
            ], 404);
        }

        return response()->json([
            'success' => true,
            'shipping_fee' => $sum
        ]);
    }

    private function calculateShippingFee($shippingMethod, $products, &$sum)
    {
        foreach ($products as $product) {
            if (isset($product['variant_id'])) {
                $variant = ProductVariant::query()
                    ->where('product_id', $product['productId'])
                    ->where('id', $product['variant_id'])
                    ->first();

                if (!$variant) {
                    return "Không tìm thấy biến thể sản phẩm với ID: {$product['variant_id']}";
                }

                $sum += $variant->$shippingMethod * $product['qty'];
            } else {
                $productModel = Product::query()->find($product['productId']);

                if (!$productModel) {
                    return "Không tìm thấy sản phẩm với ID: {$product['productId']}";
                }

                $sum += $productModel->$shippingMethod * $product['qty'];
            }
        }

        return null; // Không có lỗi
    }

    public function payBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|exists:orders,id'
        ]);

        $ids = $request->ids;

        $wallet = Wallet::firstOrCreate(
            ['user_id' => auth()->guard('web')->id()],
            ['balance' => 0]
        );

        $orders = Order::whereIn('id', $ids)
            ->where('user_id', auth()->guard('web')->id())
            ->get();

        $sum = $orders->sum('total');

        if ($sum > $wallet->balance)
            return errorResponse("Insufficient surplus to complete this transaction!", true, 400);

        DB::beginTransaction();
        try {
            $orders->map(function ($order) use ($wallet) {
                if ($order->payment_status !== "pending" || $order->status !== "pending")
                    return errorResponse("Order has already been paid!", true, 400);

                if ($wallet->balance < $order->total)
                    return errorResponse("Insufficient surplus to complete this transaction!", true, 400);

                $order->update(['payment_status' => 'completed']);

                $this->paymentViaWallet($order, $wallet);
            });

            DB::commit();
            return successResponse("Orders paid successfully.", null, 200, true);
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse("Transaction failed: " . $e->getMessage(), true, 500);
        }
    }

    public function cancelBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|exists:orders,id'
        ]);

        $ids = $request->ids;

        $orders = Order::whereIn('id', $ids)
            ->where('user_id', auth()->guard('web')->id())
            ->get();

        try {
            DB::beginTransaction();

            $orders->map(function ($order) {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'pending',
                    'canceled_by' => 'customer'
                ]);
            });

            DB::commit();
            return successResponse("Orders cancelled successfully.", null, 200, true);
        } catch (\Exception $e) {
            DB::rollBack();
            logger("message: " . $e->getMessage() . "line: " . $e->getLine());
            return errorResponse("An error occurred while canceling the order, please try again later!", true, 500);
        }
    }

    public function deleteBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|exists:orders,id'
        ]);

        $ids = $request->ids;

        $orders = Order::whereIn('id', $ids)
            ->where('user_id', auth()->guard('web')->id())
            ->get();

        if ($orders->isEmpty())
            return errorResponse("No orders found.", true, 404);

        try {
            DB::beginTransaction();

            $orders->map(function ($order) {
                if ($order->status !== "pending" || $order->payment_status !== "pending")
                    return errorResponse("Order has already been paid!", true, 400);

                $order->delete();
            });

            DB::commit();
            return successResponse("Orders deleted successfully.", null, 200, true);
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse("An error occurred while deleting the order, please try again later!", true, 500);
        }
    }
}
