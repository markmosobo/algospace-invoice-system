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
        Schema::create('restocks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('supply_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('user_id');

            $table->integer('quantity')->default(0);
            $table->decimal('buying_price', 10, 2)->default(0);

            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');

            $table->timestamps();

            // Foreign keys (optional)
            $table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restocks');
    }
};
