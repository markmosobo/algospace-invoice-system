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
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('genre')->nullable();
            $table->string('shelf_location')->nullable();

            $table->string('condition')->nullable(); // e.g. new, good, worn
            $table->string('barcode')->nullable()->unique();

            $table->unsignedBigInteger('added_by')->nullable(); // user who added the book
            $table->unsignedBigInteger('partner_id')->nullable(); // e.g. Kerry

            $table->enum('status', ['available', 'borrowed'])->default('available');

            $table->timestamps();

            // optional foreign keys if you want relationships
            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('partner_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
