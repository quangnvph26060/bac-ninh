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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã sản phẩm/kho
            $table->date('purchase_date'); // Ngày mua
            $table->decimal('price_usd', 15, 2)->nullable(); // Giá bằng USD
            $table->decimal('price_vnd', 20, 0)->nullable(); // Giá bằng VND
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse');
    }
};
