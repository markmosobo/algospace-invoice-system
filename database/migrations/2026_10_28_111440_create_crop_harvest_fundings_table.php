<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_harvest_fundings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('harvest_id')
                ->constrained('crop_harvests')
                ->cascadeOnDelete();

            $table->string('funding_source', 30);
            $table->string('transaction_type', 20);

            $table->decimal('amount', 12, 2);

            $table->date('transaction_date');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'harvest_id',
                'funding_source',
                'transaction_type',
            ]);

            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_harvest_fundings');
    }
};