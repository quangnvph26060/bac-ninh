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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 20)->unique();
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('status');
            $table->enum('contract_type', ['full-time', 'part-time', 'probation'])->default('full-time');
            $table->string('avatar')->nullable();
            $table->string('identity_card_number', 20)->nullable();
            $table->string('identity_card_image')->nullable();
            $table->text('note')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
