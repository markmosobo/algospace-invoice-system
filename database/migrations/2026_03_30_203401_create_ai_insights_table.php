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
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'insight',
                'warning',
                'projection',
                'recommendation'
            ]);

            $table->text('content');
            $table->float('confidence_score')->nullable();
            $table->json('sources')->nullable(); // payments, invoices, diary
            $table->date('relevant_from')->nullable();
            $table->date('relevant_to')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
