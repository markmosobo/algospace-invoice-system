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
        Schema::create('course_certificates', function (Blueprint $table) {

            $table->id();


            $table->foreignId('enrollment_id')
                ->constrained()
                ->cascadeOnDelete();


            // unique certificate reference
            $table->string('certificate_no')
                ->unique();


            // frozen results
            $table->decimal('percentage',5,2)
                ->default(0);


            $table->string('grade')
                ->nullable();


            // certificate metadata
            $table->date('issued_date');


            $table->string('issued_by')
                ->nullable();


            $table->string('file_path')
                ->nullable();


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_certificates');
    }
};
