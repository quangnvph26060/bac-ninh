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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('representative_name');
            $table->string('position');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('tax_code')->unique();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->unsignedBigInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
