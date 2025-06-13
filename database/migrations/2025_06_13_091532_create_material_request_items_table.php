<?php

use App\Models\Material;
use App\Models\MaterialRequest;
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
        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MaterialRequest::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Material::class)->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request_items');
    }
};
