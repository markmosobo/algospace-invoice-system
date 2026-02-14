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

            $table->enum('category', [
                'personal_savings',
                'shop_working_capital',
                'company_revenue',
                'personal_wallet',
                'dormant'
            ])->after('sub_type');

            $table->enum('account_type', [
                'cash',
                'bank',
                'mpesa',
                'sacco',
                'jar'
            ])->after('category');

            $table->enum('owner', [
                'personal',
                'shop',
                'company'
            ])->after('account_type');

            $table->boolean('is_active')
                  ->default(true)
                  ->after('owner');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'account_type',
                'owner',
                'is_active'
            ]);
        });        
    }
};
