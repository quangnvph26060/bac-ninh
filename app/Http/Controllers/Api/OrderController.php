<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function getProductDetails($items)
    {
        $productDetails = [];

        // Lấy tất cả product IDs và variant IDs
        $productIds = collect($items)->pluck('productId')->unique()->toArray();
        $variantIds = collect($items)->pluck('variant_id')->filter()->unique()->toArray();

        // Eager load products và variants
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $productId = $item['productId'];
            $qty = $item['qty'];
            $variantId = $item['variant_id'] ?? null;
            $modelImage = $item['model_image'] ?? null;
            $designImage = $item['design_image'] ?? null;

            if ($variantId && isset($variants[$variantId])) {
                $variant = $variants[$variantId];
                $product = $products[$variant->product_id];
                $price = isOnSale($variant) ? $variant->discount_price : $variant->sale_price;
                $originalPrice = $variant->sale_price;

                $productDetails[] = [
                    'name' => $product->name,
                    'id' => $productId,
                    'variant_id' => $variantId,
                    'price' => $price,
                    'original_price' => $originalPrice,
                    'qty' => $qty,
                    'total' => $price * $qty,
                    'image' => $product->image,
                    'model_image' => $modelImage,
                    'design_image' => $designImage,
                ];
            } else if (isset($products[$productId])) {
                $product = $products[$productId];
                $price = isOnSale($product) ? $product->discount_price : $product->sale_price;
                $originalPrice = $product->sale_price;

                $productDetails[] = [
                    'id' => $productId,
                    'name' => $product->name,
                    'variant_id' => null,
                    'price' => $price,
                    'original_price' => $originalPrice,
                    'qty' => $qty,
                    'total' => $price * $qty,
                    'image' => $product->image,
                    'model_image' => $modelImage,
                    'design_image' => $designImage,
                ];
            }
        }

        return $productDetails;
    }

    private function calculateShippingFee($shippingMethod, $products, &$sum)
    {
        // Lấy tất cả product IDs và variant IDs
        $productIds = collect($products)->pluck('productId')->unique()->toArray();
        $variantIds = collect($products)->pluck('variant_id')->filter()->unique()->toArray();

        // Eager load products và variants
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        foreach ($products as $product) {
            if (isset($product['variant_id']) && isset($variants[$product['variant_id']])) {
                $variant = $variants[$product['variant_id']];
                $sum += $variant->$shippingMethod * $product['qty'];
            } else if (isset($products[$product['productId']])) {
                $productModel = $products[$product['productId']];
                $sum += $productModel->$shippingMethod * $product['qty'];
            }
        }

        return null;
    }

    private function storeOrderItems($order, $productDetails)
    {
        // Tạo mảng để lưu tất cả các ảnh cần upload
        $imagesToUpload = [];
        foreach ($productDetails as $index => $product) {
            if (isset($product['model_image']) && $product['model_image'] instanceof \Illuminate\Http\UploadedFile) {
                $imagesToUpload[] = [
                    'file' => $product['model_image'],
                    'path' => "products.$index.model_image",
                    'type' => 'model_images'
                ];
            }
            if (isset($product['design_image']) && $product['design_image'] instanceof \Illuminate\Http\UploadedFile) {
                $imagesToUpload[] = [
                    'file' => $product['design_image'],
                    'path' => "products.$index.design_image",
                    'type' => 'design_images'
                ];
            }
        }

        // Upload tất cả ảnh cùng lúc
        $uploadedImages = [];
        foreach ($imagesToUpload as $image) {
            $path = uploadImages($image['path'], $image['type'], false, 150, 150, false);
            $uploadedImages[] = $path;
        }

        try {
            // Tạo tất cả order items cùng lúc
            $orderItems = [];
            foreach ($productDetails as $index => $product) {
                $modelImagePath = null;
                $designImagePath = null;

                // Tìm ảnh đã upload tương ứng
                foreach ($uploadedImages as $path) {
                    if (strpos($path, "products.$index.model_image") !== false) {
                        $modelImagePath = $path;
                    }
                    if (strpos($path, "products.$index.design_image") !== false) {
                        $designImagePath = $path;
                    }
                }

                $orderItems[] = [
                    'product_id' => $product['id'],
                    'product_variant_id' => $product['variant_id'] ?? null,
                    'product_name' => $product['name'],
                    'quantity' => $product['qty'],
                    'price' => $product['price'],
                    'original_price' => $product['original_price'],
                    'image' => $product['image'],
                    'model_image' => $modelImagePath,
                    'design_image' => $designImagePath,
                ];
            }

            // Insert tất cả order items cùng lúc
            $order->orderItems()->createMany($orderItems);

        } catch (\Exception $e) {
            logger("Lỗi xảy ra khi lưu sản phẩm: " . $e->getMessage());

            // Nếu lỗi xảy ra, xóa tất cả ảnh đã upload
            foreach ($uploadedImages as $path) {
                deleteImage($path);
            }

            throw new \Exception("Đã có lỗi xảy ra khi lưu sản phẩm. Ảnh đã được rollback.");
        }
    }
}
