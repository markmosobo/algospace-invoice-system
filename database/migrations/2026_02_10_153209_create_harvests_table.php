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
        Schema::create('harvests', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->unsignedBigInteger('crop_id'); // Link to crops
            $table->foreign('crop_id')->references('id')->on('crops')->onDelete('cascade'); 
            $table->date('harvest_date')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('unit')->nullable(); //kgs, bags, tonnes
            $table->enum('quality_grade', ['high', 'medium', 'low']);
            $table->longText('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
