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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id'); // Link to invoices
            $table->unsignedBigInteger('service_id'); // Link to services
            $table->integer('quantity')->default(1); // Quantity of service
            $table->decimal('unit_price', 10, 2); // Price per unit at time of invoice
            $table->decimal('total', 10, 2); // quantity * unit_price
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
