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
        Schema::create('course_assessments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_session_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');
            $table->enum('assessment_type', [
                'practical',
                'homework',
                'quiz',
                'assignment',
                'exam',
                'project',
                'other',
            ])->default('practical');

            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();

            $table->decimal('max_marks', 8, 2)->default(100);
            $table->decimal('pass_mark', 8, 2)->nullable();

            $table->string('attachment')->nullable();

            $table->integer('sort_order')->default(1);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assessments');
    }
};
