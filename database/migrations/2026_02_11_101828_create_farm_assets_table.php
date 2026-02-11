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
        Schema::create('farm_assets', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('asset_name'); // Required asset name
            $table->enum('asset_type', ['tool', 'machine', 'building', 'vehicle', 'other'])->default('other'); // standardized types
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 10, 2)->nullable(); // price paid
            $table->enum('condition', ['new', 'good', 'fair', 'poor', 'needs_repair'])->default('good'); // track condition
            $table->text('notes')->nullable(); // optional field for details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_assets');
    }
};
