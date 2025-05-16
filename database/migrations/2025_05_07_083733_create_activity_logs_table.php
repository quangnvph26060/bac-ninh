<?php

use App\Models\Employee;
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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained()->cascadeOnDelete(); // ID của người thực hiện
            $table->string('action'); // Hành động: create, update, delete
            $table->string('model_type'); // Tên Model, ví dụ: Product, Brand, Order
            $table->unsignedBigInteger('model_id'); // ID của bản ghi tương ứng
            $table->json('changes')->nullable(); // Thay đổi (chỉ dùng cho update)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
