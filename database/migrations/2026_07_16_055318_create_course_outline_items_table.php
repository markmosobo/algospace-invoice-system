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
        Schema::create('course_outline_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('course_outline_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('section', [
                'objective',
                'outcome',
                'requirement',
                'assessment'
            ]);

            $table->string('title');

            $table->text('description')->nullable();

            $table->integer('sort_order')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_outline_items');
    }
};
