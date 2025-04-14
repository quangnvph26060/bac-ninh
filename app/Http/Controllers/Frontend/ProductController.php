<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductVariant;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list($prefix, $suffix = null)
    {
        // Cart::instance('shopping')->destroy();

        // $products = \DB::table('products')->get();
        // $productIds = $products->pluck('id')->toArray();

        // foreach ($products as $product) {
        //     // Lấy danh sách ID khác với ID hiện tại
        //     $otherIds = array_filter($productIds, fn($id) => $id !== $product->id);

        //     // Lấy ngẫu nhiên từ 5 đến 10 ID
        //     $crossSellIds = collect($otherIds)->shuffle()->take(rand(5, 10))->values()->toArray();

        //     // Cập nhật cột cross_sell với JSON array
        //     \DB::table('products')->where('id', $product->id)->update([
        //         'cross_sell' => json_encode($crossSellIds)
        //     ]);
        // }

        // die;

        // Nếu có suffix, kiểm tra xem đó có phải là sản phẩm không
        if ($suffix) {
            $product = Product::query()->with(['variants', 'images', 'attributes'])->where('slug', $suffix)->first();
            if ($product) {
                $attributes = $product->attributes->map(function ($attribute) {
                    $valueIds = json_decode($attribute->pivot->attribute_values_ids, true);

                    // Lấy danh sách các giá trị của thuộc tính
                    $values = AttributeValue::whereIn('id', $valueIds)->pluck('value', 'id')->toArray();

                    return [
                        'name' => $attribute->name,
                        'values' => $values,
                    ];
                });

                $suggestedProducts = Product::query()->with(['variants', 'attributes'])->whereIn('id', $product->cross_sell)->get();
                $category = Category::query()->where('slug', $prefix)->firstOrFail();
                return view('frontend.pages.products.detail', compact('product', 'category', 'attributes', 'suggestedProducts'));
            }
        }

        $products = null;
        $childCategory = null;
        $category = null;

        if ($collection = Collection::query()->with('categories')->where('slug', $prefix)->first()) {
            if ($suffix) {
                $category = Category::where('slug', $suffix)
                    ->with('children')
                    ->firstOrFail();

                $products = $this->getAllProductsByCategoryRecursive($category);
            } else {
                // Nếu không có suffix → lấy toàn bộ sản phẩm thuộc các danh mục trong collection (kèm children)
                $categoryIds = [];

                foreach ($collection->categories as $cat) {
                    $cat->load('children');
                    $categoryIds = array_merge($categoryIds, $this->getAllCategoryIdsRecursive($cat));
                }

                $products = Product::whereIn('category_id', $categoryIds)
                    ->with(['variants', 'attributes', 'category'])
                    ->paginate(12);
            }

            return view('frontend.pages.products.list', compact('products', 'category', 'prefix', 'suffix', 'collection'));
        }

        // Kiểm tra xem prefix có phải là danh mục không

        if ($parentCategory = Category::with('children')->where('slug', $prefix)->first()) {
            if ($suffix) {
                // Nếu có suffix, kiểm tra xem đó có phải là danh mục con của category không
                $childCategory = Category::where('slug', $suffix)
                    ->with('children')
                    ->where('parent_id', $parentCategory->id)
                    ->first();

                if ($childCategory) {
                    $category = $childCategory;

                    $products = $this->getAllProductsByCategoryRecursive($category);
                } else {
                    abort(404);
                }
            } else {
                $category = $parentCategory;
                $products = $this->getAllProductsByCategoryRecursive($category);
            }

            return view('frontend.pages.products.list', compact('products', 'parentCategory', 'childCategory', 'category', 'prefix', 'suffix'));
        }

        // // Nếu không phải collection → kiểm tra tiếp brand
        // $brand = Brand::where('slug', $prefix)->first();
        // if ($brand) {
        //     $products = $brand->products()->paginate(12);
        //     return view('frontend.pages.products.brand', compact('products', 'brand'));
        // }

        // // Không khớp bất kỳ loại nào
        // abort(404);
    }

    public function getAllCategoryIdsRecursive(Category $category)
    {
        $ids = [$category->id];

        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIdsRecursive($child));
        }

        return $ids;
    }

    public function getAllProductsByCategoryRecursive(Category $category)
    {
        $category->load('children'); // Load để tránh N+1 query

        $allCategoryIds = $this->getAllCategoryIdsRecursive($category);

        return Product::whereIn('category_id', $allCategoryIds)->with(['variants', 'attributes', 'category'])->paginate(12);
    }



    public function selectAttribute(Request $request)
    {
        $payload = $request->validate([
            'product_id' => 'required|exists:products,id',
            'value_ids' => 'required|array|min:1',
            'value_ids.*' => 'exists:attribute_values,id'
        ], __('request.messages'), [
            'product_id' => 'sản phẩm',
            'value_ids' => 'giá trị'
        ]);

        $valueIds = collect($payload['value_ids']);

        // Lấy các variants có ít nhất 1 attribute_value đang được chọn
        $variants = ProductVariant::where('product_id', $payload['product_id'])
            ->whereHas('attributeValues', function ($query) use ($valueIds) {
                $query->whereIn('attribute_value_id', $valueIds);
            })
            ->with('attributeValues')
            ->get();

        $idsToDisable = collect();

        foreach ($variants as $variant) {
            // Nếu variant không hợp lệ
            if ($variant->stock <= 0 || $variant->stock_status !== 'in_stock') {
                $variantValueIds = $variant->attributeValues->pluck('id');
                $idsToDisable = $idsToDisable->merge($variantValueIds->diff($valueIds));
            }
        }

        return response()->json([
            'success' => true,
            'ids' => $idsToDisable->unique()->values(),
        ]);
    }

    public function findVariant(Request $request)
    {
        $productId = $request->input('product_id');
        $valueIds = $request->input('value_ids'); // array

        $variant = ProductVariant::where('product_id', $productId)
            ->whereHas('attributeValues', function ($q) use ($valueIds) {
                $q->whereIn('attribute_value_id', $valueIds);
            }, '=', count($valueIds))
            ->first();

        if (!$variant) {
            return response()->json(['message' => 'Không tìm thấy biến thể phù hợp'], 404);
        }

        return response()->json($variant);
    }
}
