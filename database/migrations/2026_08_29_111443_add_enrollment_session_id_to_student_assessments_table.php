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
        Schema::table('student_assessments', function (Blueprint $table) {
            $table->foreignId('enrollment_session_id')
                ->nullable()
                ->after('enrollment_id')
                ->constrained('enrollment_sessions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_assessments', function (Blueprint $table) {
            //
        });
    }
};
