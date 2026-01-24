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
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date')->default(now());
            $table->unsignedBigInteger('invoice_id')->nullable();
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
