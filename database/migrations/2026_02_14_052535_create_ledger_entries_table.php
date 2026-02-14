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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('entry_date');

            // where money goes
            $table->foreignId('debit_account_id')
                  ->constrained('personal_accounts')
                  ->cascadeOnDelete();

            // where money comes from
            $table->foreignId('credit_account_id')
                  ->constrained('personal_accounts')
                  ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->enum('entry_type', [
                'sale',
                'expense',
                'transfer',
                'owner_draw',
                'capital_injection',
                'loan',
                'loan_repayment'
            ]);

            $table->string('reference')->nullable();
            $table->text('description')->nullable();

            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
