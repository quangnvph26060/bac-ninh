<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Models\CompanyProduct;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductStorage;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ProductService  extends BaseService
{
    // public function __construct(Product $product, ProductStorage $productStorage)
    // {
    //     $this->product = $product;
    //     $this->productStorage = $productStorage;
    // }

    public function __construct(Product $product, public AttributeValue $attributeValue)
    {
        parent::__construct($product);
    }

    public function getProductAll()
    {

        $columns = [
            'id',
            'name',
            'brand_id',
            'category_id',
            'company_id',
            'product_unit',
            'sale_price',
            'stock',
            'status'
        ];

        $relations = ['company', 'brand', 'category'];

        return $this->queryBuilder(
            $columns,
            $relations,
            false,
            ['category_id', 'brand_id', 'company_id'],
        );
    }

    public function show(string $id)
    {
        return $this->findById($id, ['*'], ['category', 'brand', 'company', 'attributes', 'variants']);
    }

    public function getVariants($product)
    {
        return $product->variants->map(function ($variant) {
            $attributeValues = explode('-', $variant->attribute_value_combine);
            $variantName = $this->attributeValue
                ->whereIn('id', $attributeValues)
                ->get()
                ->pluck('value')
                ->implode(' - ');

            return [
                'variant_name' => $variantName,
                'sku' => $variant->sku,
                'sale_price' => $variant->sale_price,
                'discount_price' => $variant->discount_price,
                'discount_start' => !empty($variant->discount_start) ? $variant->discount_start->format('d-m-Y') : null,
                'discount_end' => !empty($variant->discount_end) ? $variant->discount_end->format('d-m-Y') : null,
                'product_unit' => $variant->product_unit,
                'stock_status' => $variant->stock_status,
                'status' => $variant->status,
                'id' => $variant->id,
                'attribute_value_combine' => $variant->attribute_value_combine,
                // 'stock' => $variant->stock,
            ];
        });
    }

    public function getProductCrossSell($product)
    {
        $crossSellIds = array_map('intval', explode(',', $product->cross_sell));

        $products = $this->all(['id', 'name', 'image'], [], [], [], ['In' => ['id', $crossSellIds]]);
        return $products;
    }

    public function attributesWithValues($product)
    {
        return $product->attributes->mapWithKeys(function ($attribute) {
            // Giải mã các giá trị đã chọn từ JSON
            $selectedValues = json_decode($attribute->pivot->attribute_values_ids);

            // Lấy thông tin thuộc tính
            $attributeName = $attribute->name;
            $attributeId = $attribute->id;  // Lấy ID của thuộc tính

            // Lấy tất cả giá trị của thuộc tính từ bảng `attribute_values`
            $allValues = $this->attributeValue::where('attribute_id', $attributeId)->get();

            // Lấy các giá trị đã chọn và ID của các giá trị đó
            $attributeValues = [];  // Mảng chứa tên và ID của tất cả giá trị của thuộc tính
            $selected = [];  // Mảng chứa tên và ID của các giá trị đã chọn

            foreach ($allValues as $value) {
                // Thêm tất cả các giá trị vào mảng `values`
                $attributeValues[$value->id] = $value->value;

                // Kiểm tra xem giá trị có trong danh sách đã chọn không
                if (in_array($value->id, $selectedValues)) {
                    // Nếu có thì thêm vào mảng `selected`
                    $selected[] = $value->id;
                }
            }

            // Sử dụng attribute_id làm key của mảng
            return [
                $attributeId => [
                    'attribute' => $attributeName,  // Trả về tên thuộc tính
                    'selected' => $selected,        // Trả về các giá trị đã chọn với ID và tên
                    'values' => $attributeValues    // Trả về tất cả giá trị của thuộc tính
                ]
            ];
        });
    }


    public function getProductAll_Staff(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            Log::info('Fetching all products');
            $product = $this->product->orderBy('created_at', 'desc')->get();
            // dd($product[0]->images[0]->image_path);
            return $product;
        } catch (Exception $e) {
            Log::error('Failed to fetch products: ' . $e->getMessage());
            throw new Exception('Failed to fetch products');
        }
    }

    public function getPRoductInStorage_Staff($id)
    {
        try {
            return $this->productStorage->orderByDesc('created_at')->where('storage_id', $id)->get();
        } catch (Exception $e) {
            Log::error('Failed to fetch product in storage: ' . $e->getMessage());
            throw new Exception('Failed to fetch product in storage');
        }
    }

    public function store(array $payload)
    {
        // dd($payload);
        $uploadedImage = null;
        $uploadedImages = null;
        return transaction(function () use ($payload, &$uploadedImage, &$uploadedImages) {

            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'products');
                $payload['image'] = $uploadedImage;
            }

            if (!empty($payload['tags'])) {
                // Giải mã chuỗi JSON thành mảng
                $tagsArray = json_decode($payload['tags'], true);

                $tags = array_map(function ($tag) {
                    return $tag['value'];
                }, $tagsArray);

                $payload['tags'] = $tags;
            }

            if (! $product = $this->create($payload)) {
                return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!');
            }

            if (hasFile('images')) {
                $uploadedImages = uploadImages('images', 'thumnails', false, 0, 0, true);

                foreach ($uploadedImages as $image) {
                    $product->images()->create([
                        'product_id' => $product->id,
                        'image' => $image,
                    ]);
                }
            }

            if (!empty($payload['attributes'])) {
                $this->productAttributes($product, $payload);
            }

            if (!empty($payload['variants'])) {
                $this->productVariants($product, $payload);
            }

            return successResponse('Thêm sản phẩm thành công', [], 201);
        }, function () use ($uploadedImage, $uploadedImages) {
            if ($uploadedImage) {
                deleteImage($uploadedImage);
            }

            if ($uploadedImages) {
                foreach ($uploadedImages as $image) {
                    deleteImage($image);
                }
            }

            return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!');
        });
    }

    protected function productVariants($product, $payload)
    {
        $variants = collect($payload['variants'])->map(fn($variant, $key) => [
            'sku'                       => $variant['sku'],
            'sale_price'                => $variant['sale_price'],
            'attribute_value_combine'   => $key,
            'discount_price'            => $variant['discount_price'] ?? null,
            'product_unit'              => $variant['product_unit'] ?? null,
            'discount_start'            => $variant['discount_start'] ?? null,
            'discount_end'              => $variant['discount_end'] ?? null,
            'stock_status'              => $variant['stock_status'],
            'status'                    =>  $variant['status'] ?? 2,
        ]);

        $product->variants()->createMany($variants);
    }

    public function productAttributes($product, $payload)
    {
        // Lấy các attribute_ids từ payload (danh sách các thuộc tính)
        $attributeIds = array_keys($payload['attributes']);  // Lấy danh sách các attribute_id (ví dụ: 15, 2)

        // Duyệt qua các thuộc tính và giá trị đã chọn
        $pivotData = [];
        foreach ($payload['attributes'] as $attributeId => $values) {
            // Chỉ lưu các giá trị được chọn cho mỗi thuộc tính
            $valueIds = [];

            foreach ($values as $value) {
                // Tách "51-Xanh" thành mảng [51, 'Xanh']
                list($valueId, $valueName) = explode('-', $value);

                // Thêm giá trị vào mảng valueIds
                $valueIds[] = (int)$valueId;  // Store the value ID as an integer
            }

            // Gắn thông tin giá trị vào bảng trung gian, bao gồm các giá trị được chọn cho mỗi thuộc tính
            $pivotData[] = [
                'attribute_id' => $attributeId,  // Lấy ID thuộc tính
                'attribute_values_ids' => json_encode($valueIds),  // Store the value IDs directly as an array
                'product_id' => $product->id
            ];
        }

        // Đồng bộ các thuộc tính vào bảng trung gian (tránh đồng bộ nhiều lần)
        if (count($pivotData)) {
            $product->attributes()->syncWithoutDetaching($pivotData);
        }
    }

    public function productByNameStaff($name)
    {
        try {
            $products = $this->product->where('name', 'LIKE', '%' . $name . '%')->orderByDesc('created_at')->get();
            // dd($products);
            return $products;
        } catch (Exception $e) {
            Log::error("Failed to search products: {$e->getMessage()}");
            throw new Exception('Failed to search products');
        }
    }

    public function productByNameInStorageStaff($name, $storage_id)
    {
        try {
            $products = $this->productStorage
                ->whereHas('product', function ($query) use ($name) {
                    $query->where('name', 'LIKE', '%' . $name . '%');
                })
                ->where('storage_id', $storage_id)
                ->with('product') // Ensure eager loading of related products
                ->get()
                ->pluck('product'); // Pluck only the product details

            return $products;
        } catch (Exception $e) {
            Log::error('Failed to find products in storage: ' . $e->getMessage());
            throw new Exception('Failed to find products in storage');
        }
    }

    public function productFilter($name, $company_id)
    {
        try {
            $query = $this->product->query();
            if ($company_id) {
                $query->whereHas('company', function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                });
            }
            if ($name) {
                $query->where('name', 'LIKE', "%{$name}%");
            }
            $products = $query->orderByDesc('created_at')->paginate(10);
            return $products;
        } catch (Exception $e) {
            Log::error("Failed to find products");
            throw new Exception("Failed to find products");
        }
    }
}
