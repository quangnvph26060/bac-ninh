<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Models\Product;

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
            'stock_status'
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
                // 'stock_status'              => $variant->stock_status,
                'status'                    => $variant->status,
                'id'                        => $variant->id,
                'attribute_value_ids'       => $variant->attributeValues->pluck('id')->implode('-'), // nếu bạn muốn dùng lại key
                'stock'                     => $variant->stock,
                'standard_shipping'         => $variant->standard_shipping,
                'express_shipping'          => $variant->express_shipping,
                'international_shipping'    => $variant->international_shipping,
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
        $uploadedImages = null;
        return transaction(function () use ($payload, &$uploadedImage, &$uploadedImages) {

            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'products', true, 800, 800);
                $payload['image'] = $uploadedImage;
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

    public function update(string $id, array $payload)
    {
        $uploadedImage = null;
        $uploadedImages = null;
        return transaction(function () use ($id, $payload, &$uploadedImage, &$uploadedImages) {
            if (!isset($payload['slug']) || !$payload['slug']) {
                $payload['slug'] = generateSlug($payload['name']);
            }

            if (hasFile('image')) {
                $uploadedImage = uploadImages('image', 'products', true, 800, 800);
                $payload['image'] = $uploadedImage;
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
                $this->productVariants($product, $payload);
            }

            return successResponse('Lưu thay đổi thành công', [], 201);
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
        $newKeys = $newVariants->keys()->toArray();
        $qty = 0;

        // Lấy các biến thể hiện tại
        $existingVariants = $product->variants()->get()->keyBy('attribute_value_combine');

        $variantsToCreate = [];
        $newVariantIds = [];

        foreach ($newVariants as $key => $variantData) {
            logger($variantData);
            $valueIds = explode('-', string: $key); // Lấy ra mảng attribute_value_id
            sort($valueIds); // Sort để đồng bộ so sánh

            $combineKey = implode('-', $valueIds);

            $data = [
                'sku'            => $variantData['sku'],
                'sale_price'     => $variantData['sale_price'],
                'discount_price' => $variantData['discount_price'] ?? null,
                'product_unit'   => $variantData['product_unit'] ?? null,
                'discount_start' => $variantData['discount_start'] ?? null,
                'discount_end'   => $variantData['discount_end'] ?? null,
                'stock'          => $variantData['stock'] ?? 0,
                'status'         => $variantData['status'] ?? 1,
                'attribute_value_combine' => $combineKey,
                'standard_shipping' => $variantData['standard_shipping'] ?? 0,
                'express_shipping' => $variantData['express_shipping'] ?? 0,
                'international_shipping' => $variantData['international_shipping'] ?? 0,
            ];

            if ($existingVariants->has($combineKey)) {
                $variant = $existingVariants[$combineKey];

                // Kiểm tra xem có thay đổi gì không
                $hasChanges = false;
                foreach ($data as $key => $value) {

                    if ($variant->$key != $value) {
                        $hasChanges = true;
                        break;
                    }
                }

                // Chỉ update nếu có thay đổi
                if ($hasChanges) {
                    $variant->update($data);
                }
            } else {
                $variant = $product->variants()->create($data);
            }

            // Sync các giá trị attribute_value cho variant
            $variant->attributeValues()->sync($valueIds);
            $newVariantIds[] = $variant->id;

            $qty += $variantData['stock'] ?? 0;
        }

        $minSalePriceVariant = $this->getMinSalePriceVariant($product);

        // Kiểm tra xem có cần update product không
        $productData = [
            'stock' => $qty,
            'sale_price' => $minSalePriceVariant->sale_price,
            'discount_price' => $minSalePriceVariant->discount_price,
            'discount_start' => $minSalePriceVariant->discount_start,
            'discount_end' => $minSalePriceVariant->discount_end,
        ];

        $hasProductChanges = false;
        foreach ($productData as $key => $value) {
            if ($product->$key != $value) {
                $hasProductChanges = true;
                break;
            }
        }

        if ($hasProductChanges) {
            $product->update($productData);
        }

        // Xóa các biến thể không còn
        $variantsToDelete = $product->variants()->whereNotIn('id', $newVariantIds)->get();
        foreach ($variantsToDelete as $variant) {
            $variant->attributeValues()->detach();
            $variant->delete();
        }
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
}
