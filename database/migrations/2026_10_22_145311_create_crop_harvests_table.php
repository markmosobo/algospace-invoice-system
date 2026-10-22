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
        Schema::create('crop_harvests', function (Blueprint $table) {
            $table->bigIncrements('id');
                    $table->foreignId('crop_id')
            ->constrained('crops')
                ->cascadeOnDelete();

            $table->date('harvest_date');
            $table->decimal('quantity_kg', 10, 2)->default(0);

            $table->decimal('labour_cost', 12, 2)->default(0);
            $table->decimal('transport_cost', 12, 2)->default(0);
            $table->decimal('other_cost', 12, 2)->default(0);

            $table->text('description')->nullable();


            $table->index(['crop_id', 'harvest_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_harvests');
    }
};
