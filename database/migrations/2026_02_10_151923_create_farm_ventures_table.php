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
        Schema::create('farm_ventures', function (Blueprint $table) {
            $table->bigIncrements(column: 'id');
            $table->string('venture_name')->nullable();
            $table->enum('venture_type', ['crop', 'livestock', 'nursery','mixed','other']);
            $table->date('start_date')->useCurrent();
            $table->enum('status', ['active', 'paused', 'closed']);
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->foreign('farm_id')
                ->references('id')->on('farms')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_ventures');
    }
};
