<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id', null);
        return [
            'name' => 'required|string|max:255|unique:products,name,' . $id,  // Product name is required, string, and max length 255
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,  // Slug is optional, but if present it must be a string
            'image' => ($id ? 'nullable' : 'required') . '|image|mimes:png,jpg,jpeg,gif,webp|max:10240',  // Image is required for new products, optional for updates, must be an image file
            'images' => 'nullable|array',
            'is_featured' => 'nullable|boolean',  // Featured flag is optional but should be a boolean (1 or 0)
            'images.*' => 'image|mimes:png,jpg,jpeg,gif,webp|max:10240',  // Images are optional but must be an array of image files
            'old' => 'nullable|array',
            'description' => 'nullable|string',  // Description is optional, but if provided, it must be a string
            'content' => 'nullable|string',  // Content is optional, but if provided, it must be a string
            'type' => 'required|string|in:variant,simple',  // Type should be 'variant'
            'sale_price' => [
                $this->input('type') == 'variant' ? 'nullable' : 'required',
                'numeric',
                'min:1',
                'regex:/^\d*(\.\d{1,2})?$/'
            ],  // Sale price should be a number and can be null, but if present, it should be >= 0
            'discount_price' => 'nullable|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',  // Discount price should be a number and can be null, but if present, it should be >= 0
            'discount_start' => 'nullable|date_format:d-m-Y H:i',  // Discount start date, optional but if present, it should be in 'd-m-Y' format
            'discount_end' => 'nullable|date_format:d-m-Y H:i|after_or_equal:discount_start',  // Discount end date, optional but if present, it should be in 'd-m-Y' format
            'stock' => 'nullable|integer|min:0',  // Stock should be a number and can be null, but if present, it should be >= 0
            'stock_status' => 'required|string|in:out_of_stock,waiting_for_goods,in_stock',  // Stock should be a number and can be null, but if present, it should be >= 0
            'product_unit' => 'nullable|string|max:255',  // Product unit is optional, but if provided, it should be a string
            'sku' => 'nullable|string|max:255',  // SKU is optional, but if provided, it should be a string
            'variants' => ($this->input('type') == 'variant' ? 'required' : 'nullable') . '|array',  // Variants are optional but should be an array if present
            // 'variants.*.sku' => 'required|string|max:255',
            'variants.*.sale_price' => 'required|numeric|min:1|regex:/^\d*(\.\d{1,2})?$/',  // Each variant must have a sale price (numeric, >= 0)
            'variants.*.product_unit' => 'nullable|string|min:0|max:100',  // Each variant must have a sale price (numeric, >= 0)
            'variants.*.discount_price' => 'nullable|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',  // Discount price for each variant (optional but should be numeric if provided)
            'variants.*.discount_start' => 'nullable|date_format:d-m-Y',  // Discount start date for each variant
            'variants.*.discount_end' => 'nullable|date_format:d-m-Y|after_or_equal:variants.*.discount_start',  // Discount end date for each variant
            'variants.*.stock' => 'nullable|numeric|min:0',  // Variant stock status should be one of the defined values
            'variants.*.stock_status' => 'required|string|in:out_of_stock,waiting_for_goods,in_stock',
            'variants.*.status' => 'nullable|numeric|in:1',  // Variant stock status should be one of the defined values
            'variants.*.standard_shipping' => 'nullable|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'variants.*.express_shipping' => 'nullable|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'variants.*.international_shipping' => 'nullable|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
            'variants.*.design_width' => 'nullable|integer|min:0',
            'variants.*.design_height' => 'nullable|integer|min:0',
            'variants.*.design_ppi' => 'nullable|integer|min:0',
            'variants.*.design_format' => 'nullable|string|in:jpg,png,gif,jpeg,webp',
            'cross_sell' => 'nullable',  // Cross-sell products are optional
            'status' => 'required|numeric|in:1,2',  // Status should be a boolean (1 or 0)
            'is_show_home' => 'nullable|boolean',  // Show on home page flag should be a boolean (1 or 0)
            'category_id' => 'nullable|integer|exists:categories,id',  // Category ID is optional, but if present, it should exist in the categories table
            'brand_id' => 'nullable|integer|exists:brands,id',  // Brand ID is optional, but if present, it should exist in the brands table
            'tags' => 'nullable',  // Tags are optional but should be an array if provided
            'attributes.*' => 'nullable|array',  // Attribute IDs are optional but should be an array if provided
            'attributes.*.*' => 'required',  // Each attribute ID must exist in the attributes table
            'standard_shipping' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'express_shipping' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'international_shipping' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'file_guideline' => 'nullable',
            'guideline_file' => 'nullable|file|mimes:zip|max:10240',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $variants = $this->input('variants');

            foreach ($variants ?? [] as $key => $variant) {
                // Kiểm tra nếu cả discount_price và sale_price đều có giá trị
                if (isset($variant['discount_price']) && isset($variant['sale_price'])) {
                    // Kiểm tra discount_price phải nhỏ hơn sale_price
                    if ($variant['discount_price'] >= $variant['sale_price']) {
                        $validator->errors()->add("variants.{$key}.discount_price", 'Discount price must be less than sale price.');
                    }
                }
            }

            $salePrice = $this->input('sale_price');
            $discountPrice = $this->input('discount_price');

            // Kiểm tra nếu cả sale_price và discount_price đều có giá trị
            if (!empty($salePrice) && !empty($discountPrice)) {
                // Kiểm tra discount_price phải nhỏ hơn sale_price
                if ($discountPrice >= $salePrice) {
                    $validator->errors()->add('discount_price', 'Discount price must be less than sale price.');
                }
            }
        });
    }

    public function messages()
    {
        return __('request.messages');
    }

    public function attributes()
    {
        return [
            'name' => 'Tên sản phẩm',
            'slug' => 'Slug',
            'description' => 'Mô tả',
            'content' => 'Nội dung',
            'type' => 'Loại sản phẩm',
            'sale_price' => 'Giá bán',
            'discount_price' => 'Giá ưu đãi',
            'discount_start' => 'Ngày bắt đầu ưu đãi',
            'discount_end' => 'Ngày kết thúc ưu đãi',
            'stock' => 'Số lượng',
            'product_unit' => 'Đơn vị sản phẩm',
            'sku' => 'Mã sản phẩm',
            'variants' => 'Biến thể sản phẩm',
            'variants.*.sku' => 'Mã sản phẩm biến thể',
            'variants.*.sale_price' => 'Giá bán biến thể',
            'variants.*.iscount_price' => 'Giá ưu đãi biến thể',
            'variants.*.discount_start' => 'Ngày bắt đầu ưu đãi biến thể',
            'variants.*.discount_end' => 'Ngày kết thúc ưu đãi biến thể',
            'variants.*.stock_status' => 'Trạng thái tồn kho biến thể',
            'variants.*.standard_shipping' => 'Vận chuyển tiêu chuẩn',
            'variants.*.express_shipping' => 'Vận chuyển nhanh',
            'variants.*.international_shipping' => 'Vận chuyển quốc tế',
            'cross_sell' => 'Sản phẩm bán kèm',
            'status' => 'Trạng thái',
            'is_show_home' => 'Hiển thị trên trang chủ',
            'category_id' => 'Danh mục sản phẩm',
            'brand_id' => 'Thương hiệu',
            'tags' => 'Tags',
            'standard_shipping' => 'Vận chuyển tiêu chuẩn',
            'express_shipping' => 'Vận chuyển nhanh',
            'international_shipping' => 'Vận chuyển quốc tế',
            'design_width' => 'Chiều rộng ảnh thiết kế',
            'design_height' => 'Chiều cao ảnh thiết kế',
            'design_ppi' => 'Độ phân giải ảnh thiết kế',
            'design_format' => 'Định dạng ảnh thiết kế',
        ];
    }
}
