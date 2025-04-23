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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function list($prefix, $suffix = null) {}

    protected function getProductAttributesForFilter($products)
    {
        $attributeMap = [];

        // Lấy tất cả các ID giá trị thuộc tính (attribute_values) mà bạn sẽ cần
        $valueIds = [];
        foreach ($products as $product) {
            foreach ($product->attributes as $attribute) {
                $valueIds = array_merge($valueIds, json_decode($attribute->pivot->attribute_values_ids, true));
            }
        }

        // Lấy tất cả các giá trị thuộc tính một lần bằng cách sử dụng eager loading
        $attributeValues = AttributeValue::whereIn('id', array_unique($valueIds))->get()->keyBy('id');

        // Bây giờ, chúng ta có thể lặp qua từng sản phẩm và thuộc tính để xây dựng attributeMap
        foreach ($products as $product) {
            foreach ($product->attributes as $attribute) {
                $attrName = $attribute->name;
                $valueIds = json_decode($attribute->pivot->attribute_values_ids, true);

                if (!isset($attributeMap[$attrName])) {
                    $attributeMap[$attrName] = [];
                }

                // Duyệt qua từng valueId và lấy giá trị thuộc tính từ collection đã eager load
                foreach ($valueIds as $valueId) {
                    if (!isset($attributeMap[$attrName][$valueId])) {
                        // Truy xuất giá trị thuộc tính từ collection đã eager load
                        $value = $attributeValues->get($valueId);
                        if ($value) {
                            $attributeMap[$attrName][$valueId] = $value->value;
                        }
                    }
                }
            }
        }

        return $attributeMap;
    }

    protected function getProductList($categoryIds, $request, &$attributes = [])
    {
        $perPage = $request->input('per_page', 10);

        $query = Product::whereIn('category_id', $categoryIds)
            ->with(['variants', 'attributes', 'category'])
            ->active();

        $this->applySearch($query, $request);
        $this->applySorting($query, $request);
        $this->applyAttributeFilters($query, $request);

        $attributes = $this->getProductAttributesForFilter($query->get());

        Log::info('SQL Query: ' . $query->toRawSql());

        return $query->paginate($perPage)->appends(request()->query());
    }

    protected function applySearch(&$query, Request $request)
    {
        if ($request->filled('search')) {
            $keyword = $request->input('search');
            $query->where('name', 'like', '%' . $keyword . '%');
        }
    }

    protected function applySorting(&$query, Request $request)
    {
        $sort = $request->input('sort');

        switch ($sort) {
            case 'price_high':
                $query->orderBy('sale_price', 'desc');
                break;
            case 'price_low':
                $query->orderBy('sale_price', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    protected function applyAttributeFilters(&$query, Request $request)
    {
        if ($request->filled('av')) {
            $attributeValueIds = $request->input('av');

            $query->whereExists(function ($subQuery) use ($attributeValueIds) {
                $subQuery->select(DB::raw(1))
                    ->from('product_attributes')
                    ->whereColumn('products.id', 'product_attributes.product_id')
                    ->where(function ($q) use ($attributeValueIds) {
                        foreach ($attributeValueIds as $id) {
                            $q->orWhereRaw("json_contains(attribute_values_ids, ?)", [$id]);
                        }
                    });
            });
        }
    }

    protected function getCategoryBreadcrumb(Category $category): array
    {
        $items = [
            [
                'label' => 'Catalog',
                'url' => route('products.all'),
            ]
        ];

        if ($category->parent) {
            $items[] = [
                'label' => $category->parent->name,
                'url' => route('products.category', $category->parent->slug),
            ];
        }

        $items[] = [
            'label' => $category->name,
        ];

        return $items;
    }

    protected function renderProductListView($categories, $products, $pageName, $items, $attributes)
    {
        if (request()->ajax()) {
            return response()->json([
                'html' => view('frontend.pages.products._product-list', compact('products'))->render(),
                'pagination' =>  $products->links('vendor.pagination.custom')->render()
            ]);
        }

        return view('frontend.pages.products.list', compact(
            'categories',
            'products',
            'pageName',
            'items',
            'attributes'
        ));
    }


    public function collection(Request $request, $slug)
    {
        $attributes = [];
        $collection = Collection::with('categories')->where('slug', $slug)->firstOrFail();
        $categories = $collection->categories ?? collect();
        $categoryIds = $categories->pluck('id');

        $products = $this->getProductList($categoryIds, $request, $attributes);
        $pageName = $collection->name;

        $items = [
            [
                'label' => 'Catalog',
                'url' => route('products.all'),
            ],
            [
                'label' => $collection->name,
            ]
        ];

        return $this->renderProductListView($categories, $products, $pageName, $items, $attributes);
    }

    public function category(Request $request, $parent, $children = null)
    {
        $attributes = [];
        $slug = $children ?? $parent;

        $category = Category::where('slug', $slug)->firstOrFail();

        // ✅ Tối ưu: Lấy toàn bộ categories và group theo parent_id
        $allCategories = Category::all()->groupBy('parent_id');

        // ✅ Lấy danh sách ID của category và các con cháu của nó
        $categoryIds = $this->getAllCategoryIdsRecursive($category, $allCategories);

        // ✅ Lấy danh sách con trực tiếp của category hiện tại (dùng để hiển thị danh mục con)
        $categories = $allCategories[$category->id] ?? collect();

        // ✅ Lấy sản phẩm
        $products = $this->getProductList($categoryIds, $request, $attributes);

        $pageName = $category->name;
        $items = $this->getCategoryBreadcrumb($category);

        return $this->renderProductListView($categories, $products, $pageName, $items, $attributes);
    }


    public function all(Request $request)
    {
        $attributes = [];
        $categories = Category::query()->whereNull('parent_id')->get();
        $query = Product::query()->active();
        $pageName = 'All Products';

        $items = [
            ['label' => 'Catalog'],
        ];

        $this->applySearch($query, $request);

        $attributes = $this->getProductAttributesForFilter($query->get());

        $perPage = $request->input('per_page', 10);

        $products = $query->paginate($perPage);

        return $this->renderProductListView($categories, $products, $pageName, $items, $attributes);
    }


    public function getAllCategoryIdsRecursive(Category $category, $allCategories)
    {
        $ids = [$category->id];

        foreach ($allCategories[$category->id] ?? [] as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIdsRecursive($child, $allCategories));
        }

        return $ids;
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
