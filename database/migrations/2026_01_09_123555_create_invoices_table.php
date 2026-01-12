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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id'); // Foreign key to customers
            $table->string('invoice_number')->unique(); // Unique invoice number
            $table->date('invoice_date'); // Date of invoice
            $table->date('due_date')->nullable(); // Optional due date
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending'); // Payment status
            $table->decimal('total_amount', 10, 2); // Total invoice amount
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
