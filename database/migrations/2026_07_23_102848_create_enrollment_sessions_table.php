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
        Schema::create('enrollment_sessions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('enrollment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_session_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->boolean('completed')
                ->default(false);


            $table->timestamp('completed_at')
                ->nullable();


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_sessions');
    }
};
