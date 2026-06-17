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

            $table->integer('duration_days')
                ->nullable()
                ->after('tier');
                    
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
