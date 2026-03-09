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
        Schema::create('borrow_records', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('borrow_date');
            $table->date('expected_return_date')->nullable();
            $table->date('return_date')->nullable();

            $table->timestamp('returned_at')->nullable();
            $table->decimal('late_fee', 8, 2)->default(0);

            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_records');
    }
};
