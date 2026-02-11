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
        Schema::create('farm_worker_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('worker_id');
            $table->foreign('worker_id')
                  ->references('id')
                  ->on('farm_workers')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('venture_id');
            $table->foreign('venture_id')
                  ->references('id')
                  ->on('farm_ventures')
                  ->onDelete('cascade');  
            $table->text('task')->nullable();
            $table->date('work_date');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_worker_tasks');
    }
};
