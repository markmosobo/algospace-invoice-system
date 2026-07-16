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
        Schema::create('course_outlines', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('overview')->nullable();
            $table->text('certificate_information')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_outlines');
    }
};
