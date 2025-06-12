<?php

use App\Models\Material;
use App\Models\MaterialUsage;
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
        Schema::create('material_usage_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MaterialUsage::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Material::class)->constrained()->restrictOnDelete();
            $table->decimal('quantity_used', 12, 2);
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_usage_details');
    }
};
