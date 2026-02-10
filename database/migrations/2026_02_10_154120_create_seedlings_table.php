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
        Schema::create('seedlings', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->unsignedBigInteger('venture_id'); // Link to farm ventures
            $table->foreign('venture_id')->references('id')->on('farm_ventures')->onDelete('cascade'); 
            $table->string('seedling_type')->nullable();  //trees, coffee, fruit, ornaments
            $table->string('species_name')->nullable();
            $table->date('date_planted')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('expected_ready_date')->nullable();
            $table->enum('survival_rate', ['high', 'medium', 'low']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seedlings');
    }
};
