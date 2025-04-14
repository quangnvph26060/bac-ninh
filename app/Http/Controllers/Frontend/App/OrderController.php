<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $search = $request->search;
        $dateRange = $request->date_range;

        $query = Order::withCount('orderItems')->with('user');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('order_code', 'like', '%' . $search . '%'); // 'code' là mã đơn hàng
        }

        if ($dateRange) {
            [$start, $end] = explode(' - ', $dateRange);
            $start = \Carbon\Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
            $end = \Carbon\Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            $html = view('frontend.components.order-table', compact('orders'))->render();
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

        return view('frontend.app.order.create', compact('products'));
    }

    public function getProducts(Request $request)
    {
        $ids = $request->input('product_ids');

        $products = Product::query()
            ->select('id', 'image', 'name', 'type', 'sale_price', 'discount_price', 'stock', 'stock_status')
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
                    'type' => $product->type
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
                    'type' => $product->type
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
            'variant_id' => $variant->id
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
