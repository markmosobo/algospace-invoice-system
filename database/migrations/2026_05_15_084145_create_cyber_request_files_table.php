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
        Schema::create('cyber_request_files', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cyber_request_id');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();

            $table->timestamps();

            $table->foreign('cyber_request_id')
                ->references('id')
                ->on('cyber_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cyber_request_files');
    }
};
