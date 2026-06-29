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
        Schema::table('services', function (Blueprint $table) {
            $table->enum('type', ['service', 'course'])
                ->default('service')
                ->after('id');

            $table->string('tier')
                ->nullable()
                ->after('type'); // basic, practical, coding, bootcamp

             // Number of Saturdays / sessions
            $table->decimal('duration_units', 4, 1)
                ->nullable()
                ->comment('number of saturday sessions, e.g. 0.5, 1, 3, 4')
                ->after('tier');

            $table->enum('schedule_type', ['saturday', 'weekday', 'custom'])
            ->default('saturday')
            ->after('duration_units');  
            
            $table->decimal('session_hours', 3, 1)
            ->default(1.5)
            ->after('schedule_type');
                    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['type', 'tier', 'duration_days']);

        });
    }
};
