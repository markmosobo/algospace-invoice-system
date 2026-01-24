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

            // For sales invoices (customer) and expense invoices (vendor)
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('vendor_name')->nullable();

            $table->string('invoice_number')->unique();
            $table->enum('invoice_type', ['sales', 'expense'])->default('sales');

            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');

            $table->decimal('total_amount', 10, 2);

            $table->timestamps();

            // Foreign key constraint (customer_id)
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
