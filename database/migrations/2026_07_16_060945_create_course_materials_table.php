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
        Schema::create('course_materials', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('course_session_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            // Display Information
            $table->string('title');
            $table->text('description')->nullable();

            // Material Type
            $table->enum('type', [
                'note',
                'ebook',
                'exercise',
                'assignment',
                'template',
                'presentation',
                'video',
                'website',
                'software',
                'other'
            ])->default('note');

            // Where the material comes from
            $table->enum('source', [
                'upload',
                'library',
                'external'
            ])->default('upload');

            // Uploaded file
            $table->string('file')->nullable();

            // Existing Library Book
            $table->foreignId('book_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // External URL
            $table->string('url')->nullable();

            // Optional display order
            $table->integer('sort_order')->default(1);

            // Optional
            $table->boolean('is_downloadable')->default(true);

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
