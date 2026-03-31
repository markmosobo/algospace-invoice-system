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
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('session_id')
            ->constrained('ai_chat_sessions')
            ->cascadeOnDelete();

            $table->enum('role', ['user', 'ai', 'system']);
            $table->text('content');
            $table->float('confidence_score')->nullable(); // 0–1
            $table->json('meta')->nullable(); // intent, tokens, model, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
    }
};
