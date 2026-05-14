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
        Schema::create('cyber_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('service');
            $table->text('message');

            $table->string('delivery_method')->nullable();
            $table->string('urgency')->nullable();

            $table->string('name');
            $table->string('email');
            $table->string('phone');

            $table->string('status')->default('pending');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cyber_requests');
    }
};
