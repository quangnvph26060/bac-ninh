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
        Schema::create('warehouse_details', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên sản phẩm
            $table->string('type')->nullable(); // Loại sản phẩm
            $table->string('name_parent')->nullable(); // Tên cha (nếu có)
            $table->integer('quantity')->default(0); // Số lượng
            $table->decimal('price', 15, 2)->default(0); // Giá tiền
            $table->string('price_type')->nullable(); // Loại giá (giá lẻ, giá sỉ,...)
            $table->string('distributor')->nullable(); // Nhà phân phối
            $table->unsignedBigInteger('warehouse_id'); // Tham chiếu tới bảng warehouse

            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_details');
    }
};
