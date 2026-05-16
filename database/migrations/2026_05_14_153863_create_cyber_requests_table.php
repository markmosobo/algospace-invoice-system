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

            $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->text('message');

            $table->string('delivery_method')->nullable();
            $table->string('urgency')->nullable();

            $table->string('payment_type')->default('prepay');

            $table->string('name');
            $table->string('email');
            $table->string('phone');

            // STATUS FLOW
            $table->enum('status', [
                'pending',
                'billed',
                'processing',
                'completed',
                'cancelled'
            ])->default('pending');

            // PAYMENT FLOW
            $table->enum('payment_status', [
                'unpaid',
                'pending',
                'paid',
                'refunded'
            ])->default('unpaid');

            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('billed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // LINK TO INVOICE
            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->nullOnDelete();

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
