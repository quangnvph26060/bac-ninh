<?php

use App\Models\ConfigPayment;
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
        Schema::create('topup_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ConfigPayment::class)->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after_topup', 15, 2)->default(0);
            $table->string('proof')->nullable();
            $table->string('transaction_code')->unique();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_requests');
    }
};
