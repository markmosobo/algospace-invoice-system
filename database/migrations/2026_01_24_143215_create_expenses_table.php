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
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->enum('type', ['expense', 'provider_service', 'inventory','other']);
            $table->unsignedBigInteger('service_provider_id')->nullable();
            $table->unsignedBigInteger('provider_service_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('expense_date')->useCurrent();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('service_provider_id')
                ->references('id')->on('service_providers')
                ->nullOnDelete();

            $table->foreign('provider_service_id')
                ->references('id')->on('provider_services')
                ->nullOnDelete();

            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->nullOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
