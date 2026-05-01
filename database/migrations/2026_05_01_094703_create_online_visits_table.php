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
        Schema::create('online_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            // identity
            $table->string('visitor_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // request info
            $table->string('ip')->nullable()->index();
            $table->text('url')->nullable();
            $table->text('user_agent')->nullable();

            // optional analytics
            $table->string('method')->nullable();
            $table->string('referer')->nullable();

            // timestamp of visit
            $table->timestamp('visited_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_visits');
    }
};
