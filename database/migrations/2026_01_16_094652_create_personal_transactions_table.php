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
        Schema::create('personal_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id'); // link to personal_accounts
            $table->unsignedBigInteger('category_id')->nullable(); // link to personal_categories
            $table->enum('type', ['income', 'expense'])->default('expense'); // money in/out
            $table->decimal('amount', 15, 2); // transaction amount
            $table->string('payment_method')->nullable(); // cash, mpesa, bank
            $table->text('description')->nullable(); // optional notes
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('personal_accounts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('personal_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_transactions');
    }
};
