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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Who performed the action
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Machine-readable event and human-readable summary
            $table->string('event', 100)->index();
            $table->string('description');

            // The affected model, e.g. Client, Invoice or Project
            $table->nullableMorphs('auditable');

            // Approved contextual details or change summaries
            $table->json('properties')->nullable();

            // Request diagnostics
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->uuid('request_id')->nullable()->index();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['created_at', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
