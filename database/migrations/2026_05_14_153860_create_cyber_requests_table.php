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
            $table->foreignId('service_id')->nullable()->constrained();

            $table->string('service');
            $table->text('message');

            $table->string('delivery_method')->nullable();
            $table->string('urgency')->nullable();
            $table->string('payment_type')->default('prepay');

            $table->string('name');
            $table->string('email');
            $table->string('phone');

            // STATUS FLOW (existing)
            $table->string('status')->default('pending'); 
            // pending | processing | completed | cancelled

            // 💰 PAYMENT ADDITIONS
            $table->string('payment_status')->default('unpaid');
            // unpaid | pending | paid | refunded

            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable(); // M-Pesa code

            $table->timestamp('paid_at')->nullable();

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
