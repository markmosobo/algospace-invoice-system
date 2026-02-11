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
        Schema::create('farm_workers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name'); // worker's full name, required
            $table->string('role')->nullable(); // e.g., harvester, caretaker
            $table->string('phone')->nullable(); // optional contact
            $table->decimal('daily_rate', 10, 2)->nullable(); // store numeric value for calculations
            $table->enum('status', ['active', 'inactive'])->default('active'); // track employment status

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_workers');
    }
};
