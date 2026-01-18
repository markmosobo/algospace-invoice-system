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
        Schema::create('personal_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name'); // e.g., "Cash", "Mpesa", "Bank Account"
            $table->decimal('balance', 15, 2)->default(0); // current balance
            $table->string('currency')->default('KES');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_accounts');
    }
};
