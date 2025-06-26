<?php

use App\Models\CashAccount;
use App\Models\CashCategory;
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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CashCategory::class)->constrained()->noActionOnDelete();
            $table->foreignIdFor(CashAccount::class)->constrained()->noActionOnDelete();
            $table->foreignId('created_by')->constrained('employees')->noActionOnDelete();
            $table->string('code')->unique();
            $table->date('date');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount');
            $table->text('description')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
