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
        Schema::table('personal_accounts', function (Blueprint $table) {
            $table->string('sub_type')->nullable()->after('name')->comment('Categorizes the source of funds, e.g. Shop Sales, Coffee Funds');
            $table->string('account_number')->nullable()->after('currency')->comment('Categorizes the source of funds, e.g. Shop Sales, Coffee Funds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_accounts', function (Blueprint $table) {
            $table->dropColumn('sub_type');
            $table->dropColumn('account_number');
        });
    }
};
