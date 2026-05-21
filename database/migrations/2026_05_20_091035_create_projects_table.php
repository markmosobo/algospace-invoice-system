<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Core info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('current_stage')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);  
                      
            // Classification
            $table->enum('type', [
                'business',
                'personal',
                'asset',
                'training'
            ])->default('personal');

            $table->enum('board_type', [
                'admin',
                'public'
            ])->nullable(); // for notice boards

            $table->enum('status', [
                'draft',
                'active',
                'blocked',
                'abandoned',
                'milestone',
                'completed',
                'archived'
            ])->default('draft');

            $table->string('cover_image')->nullable();

            // Planning
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('due_date')->nullable();

            // Control & tracking
            $table->string('blocker')->nullable(); // e.g. "Driving refresher enrollment"
            $table->unsignedTinyInteger('priority')->default(3); // 1 = high, 5 = low

            // Ownership
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};