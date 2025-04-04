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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');  // Khóa ngoại liên kết với bảng products
            $table->string('sku');  // SKU của sản phẩm variant
            $table->decimal('sale_price', 10, 2);  // Giá bán
            $table->decimal('discount_price', 10, 2)->nullable();  // Giá khuyến mãi (nếu có)
            $table->dateTime('discount_start')->nullable();  // Thời gian bắt đầu khuyến mãi
            $table->dateTime('discount_end')->nullable();  // Thời gian kết thúc khuyến mãi
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'waiting_for_goods']);  // Trạng thái tồn kho
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
