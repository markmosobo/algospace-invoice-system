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
        Schema::create('invoice_sends', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->string('channel'); // email, whatsapp
            $table->string('status')->default('sent'); // sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_sends');
    }
};
