<?php

use App\Models\Material;
use App\Models\MaterialImport;
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
        Schema::create('material_import_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MaterialImport::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Material::class)->constrained()->restrictOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 20, 2);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_import_details');
    }
};
