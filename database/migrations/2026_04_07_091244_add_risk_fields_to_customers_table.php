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
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_risky')->default(false)->after('email');
            $table->unsignedInteger('unpaid_invoices_count')->default(0)->after('is_risky');
            $table->timestamp('last_unpaid_invoice_at')->nullable()->after('unpaid_invoices_count');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['is_risky', 'unpaid_invoices_count', 'last_unpaid_invoice_at']);            
        });
    }
};
