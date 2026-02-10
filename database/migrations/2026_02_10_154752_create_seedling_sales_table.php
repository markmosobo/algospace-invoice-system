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
        Schema::create('seedling_sales', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->unsignedBigInteger('seedling_id'); // Link to seedlings
            $table->foreign('seedling_id')->references('id')->on('seedlings')->onDelete('cascade'); 
            $table->string('buyer_name')->nullable();  
            $table->integer('quantity_sold')->default(0);
            $table->decimal('price_per_unit', 10, 2);
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
        Schema::dropIfExists('seedling_sales');
    }
};
