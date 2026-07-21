<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_assessments', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->foreignId('course_assessment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('enrollment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('score', 8, 2);

            $table->decimal('percentage', 5, 2)->nullable();

            $table->string('grade')->nullable();

            $table->boolean('homework_completed')->default(false);

            $table->boolean('bonus_completed')->default(false);

            $table->text('remarks')->nullable();

            $table->string('attachment')->nullable();

            $table->timestamp('assessment_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'course_assessment_id',
                'enrollment_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_assessments');
    }
};