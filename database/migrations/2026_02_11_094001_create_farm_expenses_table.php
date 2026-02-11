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
        Schema::create('farm_expenses', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->unsignedBigInteger('venture_id'); // Link to seedlings
            $table->foreign('venture_id')->references('id')->on('farm_ventures')->onDelete('cascade'); 
            $table->enum('expense_category', ['fertilizer', 'labor', 'transport','pesticide','other']);
            $table->string('description')->nullable(); 
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('paid_by')->nullable();
            $table->string('receipt_no')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_expenses');
    }
};
