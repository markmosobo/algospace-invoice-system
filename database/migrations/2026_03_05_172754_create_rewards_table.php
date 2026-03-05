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
        Schema::create('rewards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('customer_id')
                  ->constrained()
                  ->onDelete('cascade'); // delete rewards if customer deleted
            $table->string('reward_type')->nullable(); // e.g., 'gift', 'service', 'discount'
            $table->decimal('value', 10, 2)->default(0); // reward value in KES or points
            $table->integer('visits')->default(0); // visits when reward was issued
            $table->boolean('redeemed')->default(false); // if reward was used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
