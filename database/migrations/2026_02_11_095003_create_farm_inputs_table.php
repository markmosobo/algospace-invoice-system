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
        Schema::create('farm_inputs', function (Blueprint $table) {
            $table->bigIncrements('id');
                        // Link to farm venture
            $table->unsignedBigInteger('venture_id');
            $table->foreign('venture_id')
                  ->references('id')
                  ->on('farm_ventures')
                  ->onDelete('cascade');

            // Input details
            $table->string('input_name')->nullable();
            $table->enum('input_type', ['fertilizer', 'seed', 'chemical', 'feed','other']);
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 20); // e.g., kg, bags, liters
            $table->date('date_applied');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_inputs');
    }
};
