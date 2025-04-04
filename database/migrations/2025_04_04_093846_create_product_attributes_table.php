<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');  // Khóa ngoại tới bảng products
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');  // Khóa ngoại tới bảng attributes
            $table->json('attribute_values_ids');  // Lưu mảng ID giá trị đã chọn (dạng JSON)

            $table->primary(['product_id', 'attribute_id']);  // Đặt khóa chính cho bảng là sự kết hợp giữa product_id và attribute_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
