<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;


class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        if ($request->ajax()) {
            $variant = null;

            if (!$product = Product::query()
                ->select('id', 'name', 'slug', 'image', 'sale_price', 'discount_price', 'discount_start', 'discount_end', 'product_unit', 'stock', 'stock_status', 'type')
                ->with(['variants.attributeValues', 'category'])
                ->find($request->productId)) {
                return errorResponse('Sản phẩm không tồn tại', true, 404);
            }

            $qty = $request->qty ?? 1;

            if ($product->type == 'variant') {
                $combine = collect($request->valueIds)->sort()->values()->implode('-');

                $variant = $product->variants->where('attribute_value_combine', $combine)->first();

                if (!$variant) {
                    return errorResponse('Không tìm thấy biến thể yêu cầu!', true, 404);
                }
            }

            $record = $variant ?? $product;

            // Tìm cart item theo id tương ứng (nếu là biến thể thì là id của biến thể)
            $cartItem = Cart::instance('shopping')->search(fn($item) => $item->id === $record->id)->first();

            $existingQty = $cartItem ? $cartItem->qty : 0;

            if ($this->checkStockAvailable($product, $variant, $qty + $existingQty) == false) {
                return errorResponse('Số lượng tồn kho không đủ!', true, 400);
            }

            // Tính giá
            $price = isOnSale($record)
                ? $record->discount_price
                : $record->sale_price;

            if ($price <= 0) {
                return errorResponse('Giá sản phẩm không hợp lệ.', true, 400);
            }

            if ($cartItem) {
                Cart::instance('shopping')->update($cartItem->rowId, $cartItem->qty + $qty);
            } else {
                Cart::instance('shopping')->add([
                    'id' => $record->id,
                    'name' => $product->name,
                    'qty' => $qty,
                    'price' => $price,
                    'options' => [
                        'variant' => $variant ? $variant->attributeValues->pluck('value')->implode(' / ') : null,
                        'image' => $product->image,
                        'slug' => $product->slug,
                        'catalogue' => $product->category->name ?? ''
                    ]
                ]);
            }

            $carts = Cart::instance('shopping');
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'count' => $carts->count(),
            ]);
        }
    }

    public function checkStockAvailable($product, $variant = null, $qty = 1): bool
    {
        $record = $variant ?? $product;

        // Nếu không quản lý tồn kho
        if ($record->stock_status === 'out_of_stock') {
            return false;
        }

        // Kiểm tra số lượng tồn kho hiện tại
        return $record->stock > $qty;
    }
}
