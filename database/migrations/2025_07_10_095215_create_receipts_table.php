<?php

use App\Models\VoucherType;
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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('money_account_id')->nullable()->constrained('cash_accounts')->nullOnDelete();
            $table->foreignIdFor(VoucherType::class)->nullable()->constrained()->nullOnDelete();
            $table->date('transaction_date');
            $table->enum('type', ['receipt', 'payment']);
            $table->enum('object_type', ['customer', 'supplier', 'employee', 'other']);
            $table->morphs('objectable');
            $table->decimal('amount', 18, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
