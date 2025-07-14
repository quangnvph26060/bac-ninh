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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã khách hàng
            $table->string('name')->index();  // Tên khách hàng
            $table->string('phone')->index(); // Số điện thoại
            $table->string('email')->nullable(); // Email
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('address')->nullable(); // Địa chỉ chi tiết

            $table->string('company_name')->nullable();        // Tên công ty
            $table->string('company_tax_code')->nullable();    // Mã số thuế
            $table->string('company_address')->nullable();     // Địa chỉ công ty

            $table->string('citizen_id')->nullable();          // Số CCCD/CMND

            $table->text('note')->nullable();                  // Ghi chú

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
