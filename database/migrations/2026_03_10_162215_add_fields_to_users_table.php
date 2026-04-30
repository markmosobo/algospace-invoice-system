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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->date('dob')->nullable()->after('phone');
            $table->string('address')->nullable()->after('dob');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('city');
            $table->enum('membership_type', ['basic','student', 'staff', 'public', 'premium'])
                  ->default('public')
                  ->after('postal_code');
            $table->integer('borrow_limit')->default(3)->after('membership_type');
            $table->enum('status', ['active', 'pending', 'suspended'])->default('active')->after('borrow_limit');

            // Profile photo/file path
            $table->string('profile_photo_file')->nullable()->after('role'); // for uploaded file
            $table->string('profile_photo_url')->nullable()->after('profile_photo_file'); // for URL 
            // stores either uploaded image path or URL/path to file
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the columns added in up()
            $table->dropColumn([
                'phone',
                'dob',
                'address',
                'city',
                'postal_code',
                'membership_type',
                'borrow_limit',
                'status',
                'profile_photo_file',
                'profile_photo_url'
            ]);
        });
    }
};
