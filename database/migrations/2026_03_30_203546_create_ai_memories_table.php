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
        Schema::create('ai_memories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('category', [
                'behavior',
                'preference',
                'pattern',
                'risk',
                'goal'
            ]);

            $table->string('key'); // e.g. "late_invoice_pattern"
            $table->text('value');
            $table->integer('importance')->default(1); // 1–5
            $table->timestamp('last_observed_at')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_memories');
    }
};
