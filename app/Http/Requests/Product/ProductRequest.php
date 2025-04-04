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
            'image' => ($id ? 'nullable' : 'required') . '|image|mimes:png,jpg,jpeg,gif,webp|max:2048',  // Image is required for new products, optional for updates, must be an image file
            'images' => 'nullable|array',
            'is_featured' => 'nullable|boolean',  // Featured flag is optional but should be a boolean (1 or 0)
            'images.*' => 'image|mimes:png,jpg,jpeg,gif,webp|max:2048',  // Images are optional but must be an array of image files
            'description' => 'nullable|string',  // Description is optional, but if provided, it must be a string
            'content' => 'nullable|string',  // Content is optional, but if provided, it must be a string
            'type' => 'required|string|in:variant,simple',  // Type should be 'variant'
            'sale_price' => 'nullable|numeric|min:0',  // Sale price should be a number and can be null, but if present, it should be >= 0
            'discount_price' => 'nullable|numeric|min:0',  // Discount price should be a number and can be null, but if present, it should be >= 0
            'discount_start' => 'nullable|date_format:d-m-Y',  // Discount start date, optional but if present, it should be in 'd-m-Y' format
            'discount_end' => 'nullable|date_format:d-m-Y',  // Discount end date, optional but if present, it should be in 'd-m-Y' format
            'stock' => 'nullable|integer|min:0',  // Stock should be a number and can be null, but if present, it should be >= 0
            'product_unit' => 'nullable|string|max:255',  // Product unit is optional, but if provided, it should be a string
            'sku' => 'nullable|string|max:255',  // SKU is optional, but if provided, it should be a string
            'variants' => 'nullable|array',  // Variants are optional but should be an array if present
            'variants.*.sku' => 'required|string|max:255',  // Each variant must have a SKU (string)
            'variants.*.sale_price' => 'required|numeric|min:0',  // Each variant must have a sale price (numeric, >= 0)
            'variants.*.product_unit' => 'nullable|string|min:0|max:100',  // Each variant must have a sale price (numeric, >= 0)
            'variants.*.discount_price' => 'nullable|numeric|min:0',  // Discount price for each variant (optional but should be numeric if provided)
            'variants.*.discount_start' => 'nullable|date_format:d-m-Y',  // Discount start date for each variant
            'variants.*.discount_end' => 'nullable|date_format:d-m-Y',  // Discount end date for each variant
            'variants.*.stock_status' => 'required|string|in:out_of_stock,waiting_for_goods,in_stock',  // Variant stock status should be one of the defined values
            'variants.*.status' => 'nullable|numeric|in:1',  // Variant stock status should be one of the defined values
            'cross_sell' => 'nullable',  // Cross-sell products are optional
            'status' => 'required|numeric|in:1,2',  // Status should be a boolean (1 or 0)
            'is_show_home' => 'required|boolean',  // Show on home page flag should be a boolean (1 or 0)
            'category_id' => 'nullable|integer|exists:categories,id',  // Category ID is optional, but if present, it should exist in the categories table
            'brand_id' => 'nullable|integer|exists:brands,id',  // Brand ID is optional, but if present, it should exist in the brands table
            'tags' => 'nullable',  // Tags are optional but should be an array if provided
            'attributes.*' => 'nullable|array',  // Attribute IDs are optional but should be an array if provided
            'attributes.*.*' => 'required',  // Each attribute ID must exist in the attributes table
        ];
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
            'cross_sell' => 'Sản phẩm bán kèm',
            'status' => 'Trạng thái',
            'is_show_home' => 'Hiển thị trên trang chủ',
            'category_id' => 'Danh mục sản phẩm',
            'brand_id' => 'Thương hiệu',
            'tags' => 'Tags',
        ];
    }
}
