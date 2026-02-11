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
        Schema::create('farm_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Link to farm venture
            $table->unsignedBigInteger('venture_id');
            $table->foreign('venture_id')
                  ->references('id')
                  ->on('farm_ventures')
                  ->onDelete('cascade');
            $table->string('product_name')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 20); // e.g., kg, bags, liters 
            $table->decimal('price_per_unit', 10, 2);
            $table->string('buyer')->nullable();
            $table->date('sale_date')->useCurrent();
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_sales');
    }
};
