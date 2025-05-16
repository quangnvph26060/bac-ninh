<?php

use App\Models\User;
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
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Số tiền chuyển khoản
            $table->enum('transaction_type', ['recharge', 'withdraw'])->default('recharge'); // Ví dụ: 'nạp tiền', 'rút tiền', v.v.
            $table->enum('status', ['complete', 'processing', 'failure'])->default('processing'); // Ví dụ: 'hoàn thành', 'đang xử lý', 'thất bại'
            $table->text('note')->nullable(); // Ghi chú thêm nếu cần
            $table->text('transaction_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
    }
};
