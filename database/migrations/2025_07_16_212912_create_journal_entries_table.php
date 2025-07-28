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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['income', 'expense', 'other', 'debit_notice', 'credit_notice'])->default('other');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('note')->nullable();
            $table->morphs('tableable');
            $table->date('transaction_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
