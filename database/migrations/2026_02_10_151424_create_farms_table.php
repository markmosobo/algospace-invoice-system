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
        Schema::create('farms', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->decimal('size', 10, 2); //in hectares
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('owner_id'); // Link to users
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
