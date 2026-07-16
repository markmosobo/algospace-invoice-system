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
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('session_number');

            $table->string('title');

            $table->text('description')->nullable();

            $table->decimal('duration_hours', 4, 2)->nullable();

            $table->integer('sort_order')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};
