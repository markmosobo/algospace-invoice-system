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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id'); // Link to invoice
            $table->decimal('amount', 10, 2); // Amount paid
            $table->timestamp('payment_date')->useCurrent(); // Payment date
            $table->enum('method', ['cash', 'mpesa', 'card', 'other'])->default('cash'); // Payment method
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
