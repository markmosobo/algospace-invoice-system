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

            $table->unsignedBigInteger('invoice_id');

            // Item type: cyber service or provider service
            $table->enum('item_type', ['cyber_service', 'provider_service', 'expense'])->default('cyber_service');

            // Cyber service item fields
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('service_name')->nullable();

            // Provider service item fields (services you pay for)
            $table->unsignedBigInteger('provider_service_id')->nullable();
            $table->string('provider_service_name')->nullable();

            // Extra expense name (if needed)
            $table->string('expense_name')->nullable();

            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('line_total', 10, 2);

            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('provider_service_id')->references('id')->on('provider_services')->onDelete('cascade');
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
