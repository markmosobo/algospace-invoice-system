<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Add season and populate existing harvest records.
     *
     * Assumption: farming season runs July to June.
     */
    public function up(): void
    {
        Schema::table('crop_harvests', function (Blueprint $table) {
            $table->string('season', 9)
                ->nullable()
                ->after('harvest_date')
                ->index();
        });

        // Backfill historical records using their actual harvest dates.
        DB::table('crop_harvests')
            ->select('id', 'harvest_date')
            ->orderBy('id')
            ->chunkById(500, function ($harvests) {
                foreach ($harvests as $harvest) {
                    $date = Carbon::parse($harvest->harvest_date);

                    $startYear = $date->month >= 7
                        ? $date->year
                        : $date->year - 1;

                    $season = $startYear . '/' .
                        substr((string) ($startYear + 1), -2);

                    DB::table('crop_harvests')
                        ->where('id', $harvest->id)
                        ->update(['season' => $season]);
                }
            });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('crop_harvests', function (Blueprint $table) {
            $table->dropIndex(['season']);
            $table->dropColumn('season');
        });
    }
};