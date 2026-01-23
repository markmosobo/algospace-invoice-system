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
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');                // Service name
            $table->string('category');            // Printing & Copying, Bundles, etc

            $table->decimal('price', 10, 2);       // Service price (KES)
            $table->string('unit');                // page, hour, service, bundle, document

            $table->boolean('is_bundle')->default(false); // Bundle or not

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
