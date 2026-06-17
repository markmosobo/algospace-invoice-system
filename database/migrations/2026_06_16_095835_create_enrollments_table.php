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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->bigIncrements('id');

            // RELATIONSHIPS
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // your course in services table


            // STATUS FLOW (THIS IS KEY)
            $table->string('status')->default('pending');
            // pending → active → completed → dropped

            // PAYMENT LINK (future-proofing)
            $table->boolean('is_paid')->default(false);
            $table->decimal('amount_paid', 10, 2)->default(0);

            $table->timestamp('paid_at')->nullable();

            // COURSE PROGRESS (optional but powerful later)
            $table->integer('progress_percent')->default(0);

            // IMPORTANT DATES
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
