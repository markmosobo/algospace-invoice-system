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
        Schema::create('todos', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Task details
            $table->string('title')->nullable(); // Task title
            $table->text('description')->nullable(); // Optional detailed description
            $table->enum('category', ['cyber', 'farm', 'personal', 'other'])->default('other'); // Logical categories
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium'); // Task priority
            
            // Status: pending, in progress, completed, deferred, delegated
            $table->enum('status', ['pending', 'in_progress', 'completed', 'deferred', 'delegated'])->default('pending');
            
            $table->timestamp('checked_at')->nullable(); // When task was counter-checked/verified
            
            // Delegation
            $table->unsignedBigInteger('delegated_to')->nullable(); // Staff assigned if delegated
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Safe deletion
            
            // Foreign key to users table for delegation
            $table->foreign('delegated_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
