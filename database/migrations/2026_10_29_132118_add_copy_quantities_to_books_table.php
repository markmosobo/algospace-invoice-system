<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedInteger('total_copies')
                ->default(1)
                ->after('shelf_location');

            $table->unsignedInteger('borrowed_copies')
                ->default(0)
                ->after('available_copies');
        });

        // Preserve the current one-record-per-book availability state.
        DB::table('books')
            ->where('status', 'borrowed')
            ->update([
                'borrowed_copies' => 1,
            ]);
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'total_copies',
                'available_copies',
                'borrowed_copies',
            ]);
        });
    }
};