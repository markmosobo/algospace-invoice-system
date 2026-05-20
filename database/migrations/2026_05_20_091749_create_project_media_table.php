<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_media', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('project_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // File info
            $table->string('file_path');
            $table->string('file_name')->nullable();

            // Type of media
            $table->enum('type', [
                'image',
                'video',
                'document'
            ])->default('image');

            // Progress tracking (VERY IMPORTANT for your use case)
            $table->string('caption')->nullable();

            $table->text('notes')->nullable();

            // Optional: milestone tagging
            $table->string('stage')->nullable(); 
            // e.g. foundation, roofing, finishing

            // Who uploaded it
            $table->foreignId('uploaded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_media');
    }
};