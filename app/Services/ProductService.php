<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductService  extends BaseService
{

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
            'status',
            'type',
            'stock_status',
            'sku',
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
        return $this->findById($id, ['*'], ['category', 'brand', 'company', 'attributes', 'variants.attributeValues']);
    }

    public function getVariants($product)
    {
        // Load sẵn attributeValues nếu chưa có
        $product->loadMissing('variants.attributeValues');

        return $product->variants->map(function ($variant) {
            $variantName = $variant->attributeValues
                ->pluck('value')
                ->implode(' - ');

            return [
                'variant_name'              => $variantName,
                'sku'                       => $variant->sku,
                'sale_price'                => $variant->sale_price,
                'discount_price'            => $variant->discount_price,
                'discount_start'            => optional($variant->discount_start)->format('d-m-Y H:i'),
                'discount_end'              => optional($variant->discount_end)->format('d-m-Y H:i'),
                'product_unit'              => $variant->product_unit,
                'stock_status'              => $variant->stock_status,
                'status'                    => $variant->status,
                'id'                        => $variant->id,
                'attribute_value_ids'       => $variant->attributeValues->pluck('id')->implode('-'), // nếu bạn muốn dùng lại key
                'stock'                     => $variant->stock,
                'standard_shipping'         => $variant->standard_shipping,
                'express_shipping'          => $variant->express_shipping,
                'international_shipping'    => $variant->international_shipping,
                'design_width'              => $variant['design_width'],
                'design_height'             => $variant['design_height'],
                'design_ppi'                => $variant['design_ppi'],
                'design_format'             => $variant['design_format'],
            ];
        });
    }

    public function getProductCrossSell($product)
    {
        $crossSellIds = array_map('intval', $product->cross_sell ?? []);
        $products = $this->all(['id', 'name', 'image'], [], [], [], ['In' => ['id', $crossSellIds]]);
        return $products;
    }

    public function getProductImages($product)
    {
        return $product->images->map(fn($image) => [
            'id' => $image->id,
            'src' => showImage($image->image),
        ]);
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


    public function store(array $payload)
    {
        $uploadedImage = null;
        $uploadTemplate = null;
        return transaction(function () use ($payload, &$uploadedImage, &$uploadTemplate) {

            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'products', true, 800, 800);
                $payload['image'] = $uploadedImage;
            }

            if (hasFile('guideline_file')) {
                $uploadTemplate = uploadZipFile('guideline_file', 'guideline_file');
                $payload['guideline_file'] = $uploadTemplate;
            }


            if (!empty($payload['cross_sell'])) {
                $payload['cross_sell'] = array_map('intval', explode(',', $payload['cross_sell']));
            }

            if (!empty($payload['tags'])) {
                // Giải mã chuỗi JSON thành mảng
                $tagsArray = json_decode($payload['tags'], true);

                $tags = array_map(fn($tag) => $tag['value'], $tagsArray);

                $payload['tags'] = $tags;
            }

            if ($payload['type'] == 'variant') {
                unset($payload['sale_price']);
                unset($payload['discount_price']);
                unset($payload['discount_start']);
                unset($payload['discount_end']);
                unset($payload['product_unit']);
                unset($payload['stock']);
                unset($payload['standard_shipping']);
                unset($payload['express_shipping']);
                unset($payload['international_shipping']);
            }

            if (! $product = $this->create($payload)) {
                return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!');
            }

            $this->productImage($product, $payload);

            if (!empty($payload['attributes'])) {
                $this->productAttributes($product, $payload);
            }

            if (!empty($payload['variants'])) {
                $this->productVariants($product, $payload);
            }

            return successResponse('Thêm sản phẩm thành công', [], 201);
        }, function () use ($uploadedImage, $uploadTemplate) {
            if ($uploadedImage) {
                deleteImage($uploadedImage);
            }

            if ($uploadTemplate) {
                deleteImage($uploadTemplate);
            }

            return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!');
        });
    }

    public function update(string $id, array $payload)
    {

        $uploadedImage = null;
        $uploadTemplate = null;
        return transaction(function () use ($id, $payload, &$uploadedImage, &$uploadTemplate) {
            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'products', true, 800, 800);
                $payload['image'] = $uploadedImage;
            }

            if (hasFile('guideline_file')) {
                $uploadTemplate = uploadZipFile('guideline_file', 'guideline_file');
                $payload['guideline_file'] = $uploadTemplate;
            }

            if (!empty($payload['tags'])) {
                $tagsArray = json_decode($payload['tags'], true);

                $tags = array_map(fn($tag) => $tag['value'], $tagsArray);

                $payload['tags'] = $tags;
            }

            if (!empty($payload['cross_sell'])) {
                $payload['cross_sell'] = array_map('intval', explode(',', $payload['cross_sell']));
            }

            if ($payload['type'] == 'variant') {
                unset($payload['sale_price']);
                unset($payload['discount_price']);
                unset($payload['discount_start']);
                unset($payload['discount_end']);
                unset($payload['product_unit']);
                unset($payload['stock']);
                unset($payload['standard_shipping']);
                unset($payload['express_shipping']);
                unset($payload['international_shipping']);
            }

            $payload['is_featured'] ??= 0;

            if (!$product = $this->updateData($id, $payload)) {
                return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!');
            }

            $this->productImage($product, $payload);

            if (!empty($payload['attributes'])) {
                $this->productAttributes($product, $payload);
            }

            if (!empty($payload['variants'])) {
                $result = $this->productVariants($product, $payload);
                if ($result !== true) return $result; // Trả lỗi nếu SKU trùng
            }

            return successResponse('Lưu thay đổi thành công', [], 201);
        }, function () use ($uploadedImage, $uploadTemplate) {
            if ($uploadedImage) {
                deleteImage($uploadedImage);
            }
            if ($uploadTemplate) {
                deleteImage($uploadTemplate);
            }
            return errorResponse('Có lỗi xảy ra. Vui lòng thử lại sau!');
        });
    }

    public function productImage($product, $payload)
    {
        $oldImages = $product->images()->whereNotIn('id', $payload['old'] ?? [])->get();
        if ($oldImages->isNotEmpty()) {
            foreach ($oldImages as $oldImage) {
                // Xóa ảnh từ storage
                deleteImage($oldImage->image);
                // Xóa bản ghi trong cơ sở dữ liệu
                $oldImage->delete();
            }
        };

        if (hasFile('images')) {
            $uploadedImages = uploadImages('images', 'thumnails', false, 0, 0, true);

            foreach ($uploadedImages as $image) {
                $product->images()->create([
                    'product_id' => $product->id,
                    'image' => $image,
                ]);
            }
        }
    }

    protected function productVariants($product, $payload)
    {
        $newVariants = collect($payload['variants']);
        $existingVariants = $product->variants()->get()->keyBy('attribute_value_combine');
        $newVariantIds = [];
        $qty = 0;

        $attributeValues = $this->getAttributeValues($newVariants);
        $generatedSkus = $this->generateSkus($product, $newVariants, $attributeValues);

        if ($error = $this->validateSkus($generatedSkus, $existingVariants)) {
            return $error;
        }

        foreach ($newVariants as $key => $variantData) {
            $valueIds = explode('-', $key);
            sort($valueIds);
            $combineKey = implode('-', $valueIds);
            $variantSku = $generatedSkus[$combineKey];

            $data = $this->prepareVariantData($variantData, $variantSku, $combineKey);
            $variant = $this->updateOrCreateVariant($product, $existingVariants, $combineKey, $data);

            $variant->attributeValues()->sync($valueIds);
            $newVariantIds[] = $variant->id;
            $qty += $data['stock'];
        }

        $this->updateProductData($product, $qty);
        $this->cleanupOldVariants($product, $newVariantIds);

        return true;
    }

    protected function getAttributeValues($newVariants)
    {
        $allValueIds = $newVariants->keys()
            ->flatMap(fn($key) => explode('-', $key))
            ->unique()
            ->values();

        return AttributeValue::whereIn('id', $allValueIds)->get()->keyBy('id');
    }

    protected function generateSkus($product, $newVariants, $attributeValues)
    {
        $generatedSkus = [];
        foreach ($newVariants as $key => $variantData) {
            $valueIds = explode('-', $key);
            sort($valueIds);
            $combineKey = implode('-', $valueIds);

            $skuParts = [];
            foreach ($valueIds as $id) {
                $value = $attributeValues[$id]->value ?? null;
                if ($value) {
                    $words = explode(' ', trim($value));
                    if (count($words) === 1) {
                        $skuParts[] = strtoupper(removeVietnameseTones($words[0]));
                    } else {
                        $wordsUpper = array_map(function ($w) {
                            return strtoupper(removeVietnameseTones($w));
                        }, $words);
                        $skuParts[] = implode(' ', $wordsUpper);
                    }
                }
            }

            $variantSku = strtoupper(removeVietnameseTones($product->sku)) . '-' . implode('-', $skuParts);
            $generatedSkus[$combineKey] = $variantSku;
        }
        return $generatedSkus;
    }

    protected function validateSkus($generatedSkus, $existingVariants)
    {
        $existingSkuRecords = ProductVariant::whereIn('sku', array_values($generatedSkus))->get();
        $existingSkuMap = $existingSkuRecords->keyBy('sku');

        foreach ($generatedSkus as $combineKey => $variantSku) {
            if (
                isset($existingSkuMap[$variantSku]) &&
                (!isset($existingVariants[$combineKey]) || $existingVariants[$combineKey]->id !== $existingSkuMap[$variantSku]->id)
            ) {
                return errorResponse("SKU biến thể [$variantSku] đã tồn tại.", false, 422);
            }
        }
        return null;
    }

    protected function prepareVariantData($variantData, $variantSku, $combineKey)
    {
        return [
            'sku' => $variantSku,
            'sale_price' => $variantData['sale_price'],
            'discount_price' => $variantData['discount_price'] ?? null,
            'product_unit' => $variantData['product_unit'] ?? null,
            'discount_start' => $variantData['discount_start'] ?? null,
            'discount_end' => $variantData['discount_end'] ?? null,
            'stock' => $variantData['stock'] ?? 0,
            'status' => $variantData['status'] ?? 1,
            'stock_status' => $variantData['stock_status'],
            'attribute_value_combine' => $combineKey,
            'standard_shipping' => $variantData['standard_shipping'] ?? 0,
            'express_shipping' => $variantData['express_shipping'] ?? 0,
            'international_shipping' => $variantData['international_shipping'] ?? 0,
            'design_width' => $variantData['design_width'],
            'design_height' => $variantData['design_height'],
            'design_ppi' => $variantData['design_ppi'],
            'design_format' => $variantData['design_format'],
        ];
    }

    protected function updateOrCreateVariant($product, $existingVariants, $combineKey, $data)
    {
        if (isset($existingVariants[$combineKey])) {
            $variant = $existingVariants[$combineKey];
            $hasChanges = collect($data)->some(fn($v, $k) => $variant->$k != $v);
            if ($hasChanges) {
                $variant->update($data);
            }
            return $variant;
        }
        return $product->variants()->create($data);
    }

    protected function updateProductData($product, $qty)
    {
        $minSalePriceVariant = $this->getMinSalePriceVariant($product);
        if (!$minSalePriceVariant) return;

        $productData = [
            'stock' => $qty,
            'sale_price' => $minSalePriceVariant->sale_price,
            'discount_price' => $minSalePriceVariant->discount_price,
            'discount_start' => $minSalePriceVariant->discount_start,
            'discount_end' => $minSalePriceVariant->discount_end,
        ];

        $hasProductChanges = collect($productData)->some(fn($v, $k) => $product->$k != $v);
        if ($hasProductChanges) {
            $product->update($productData);
        }
    }

    protected function cleanupOldVariants($product, $newVariantIds)
    {
        $product->variants()
            ->whereNotIn('id', $newVariantIds)
            ->get()
            ->each(function ($variant) {
                $variant->attributeValues()->detach();
                $variant->delete();
            });
    }

    protected function getMinSalePriceVariant($product)
    {
        // Lấy tất cả các biến thể của sản phẩm và lọc theo điều kiện stock > 0 và status = 1
        $variants = $product->variants()->where('status', 1)->get();

        // Kiểm tra nếu không có biến thể hợp lệ
        if ($variants->isEmpty()) {
            return null; // Nếu không có biến thể hợp lệ, trả về null
        }

        // Tìm biến thể có giá sale_price nhỏ nhất
        $minSalePriceVariant = $variants->sortBy('sale_price')->first();

        return $minSalePriceVariant;
    }

    public function productAttributes($product, $payload)
    {
        // Lấy danh sách attribute_id từ payload
        $newAttributes = $payload['attributes']; // [15 => ["51-Xanh", "52-Đỏ"], 2 => ["6-S", "7-M"]]
        $newAttributeIds = array_keys($newAttributes);

        // Lấy các bản ghi hiện tại từ bảng trung gian
        $existingAttributes = $product->attributes()->get()->keyBy('id'); // keyBy attribute_id

        $syncData = [];

        foreach ($newAttributes as $attributeId => $values) {
            $valueIds = [];

            foreach ($values as $value) {
                [$valueId, $valueName] = explode('-', $value);
                $valueIds[] = (int) $valueId;
            }

            // Thêm vào dữ liệu cần sync
            $syncData[$attributeId] = [
                'attribute_values_ids' => json_encode($valueIds)
            ];
        }

        // Đồng bộ: sẽ tự động xóa các attribute_id không còn trong $syncData, cập nhật cái có và thêm cái mới
        $product->attributes()->sync($syncData);
    }

    public function productSelect($request)
    {
        $query = $this->model->query();

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%");
            });
        }

        $products = $query->orderByDesc('created_at')
            ->paginate(20);

        $results = $products->map(fn($product) => [
            'id' => $product->id,
            'name' => $product->name,
        ]);

        return [
            'data' => $results,
            'next_page_url' => $products->nextPageUrl(),
        ];
    }
}
