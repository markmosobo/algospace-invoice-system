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
        Schema::create('diary_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable(); // short title of the entry
            $table->enum('type', ['note', 'credit', 'debit', 'reminder', 'event'])->default('note');
            $table->decimal('amount', 15, 2)->nullable(); // only for credit/debit
            $table->enum('category', ['office', 'personal'])->default('office');
            $table->string('tags')->nullable(); // optional tags
            $table->text('description')->nullable(); // detailed description
            $table->string('attachment')->nullable(); // optional file path
            $table->timestamp('entry_date')->useCurrent();
            $table->timestamp('remind_at')->nullable();  // reminder trigger time
            $table->enum('status', ['pending', 'done', 'overdue'])->default('pending'); // for reminders/events
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_entries');
    }
};
