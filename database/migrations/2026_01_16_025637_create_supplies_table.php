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
        Schema::create('supplies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('supplier_id'); // Link to invoice
            $table->string('item')->nullable(); //for product
            $table->integer('unit_price')->nullable(); //for price per item
            $table->integer('quantity')->nullable(); //for keeping count
            $table->decimal('total', 10, 2); // quantity * unit_price
            $table->timestamp('payment_date')->useCurrent(); // Payment date
            $table->enum('payment_method', ['cash', 'mpesa', 'card', 'other'])->default('cash'); // Payment method
            $table->enum('status', ['replenisheble', 'onetime', 'other'])->default('replenisheble'); // Status
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
