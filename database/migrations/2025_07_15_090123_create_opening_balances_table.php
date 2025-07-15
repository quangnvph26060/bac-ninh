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
        Schema::create('opening_balances', function (Blueprint $table) {
            $table->id();

            $table->date('transaction_date');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 20, 2)->default(0);
            $table->string('note')->nullable();
            $table->enum('object_type', ['customer', 'supplier', 'employee']);
            $table->foreignId('object_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_balances');
    }
};
