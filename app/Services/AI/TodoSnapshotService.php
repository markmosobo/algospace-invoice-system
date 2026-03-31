<?php

namespace App\Services\AI;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TodoSnapshotService
{
    public static function thisWeek(): array
    {
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();

        $total = DB::table('todos')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $completed = DB::table('todos')
            ->whereBetween('updated_at', [$start, $end])
            ->where('status', 'completed')
            ->count();

        $active = DB::table('todos')
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        return [
            'period' => 'this_week',
            'total_tasks' => $total,
            'completed_tasks' => $completed,
            'active_tasks' => $active,
            'completion_rate' => $total > 0
                ? round(($completed / $total) * 100, 1)
                : null,
        ];
    }

    public static function today(): array
    {
        $today = Carbon::today();

        $createdToday = DB::table('todos')
            ->whereDate('created_at', $today)
            ->count();

        $completedToday = DB::table('todos')
            ->whereDate('updated_at', $today)
            ->where('status', 'completed')
            ->count();

        return [
            'period' => 'today',
            'created_today' => $createdToday,
            'completed_today' => $completedToday,
        ];
    }

    /**
     * Procrastination = active tasks that have not moved
     * for more than N days
     */
    public static function procrastination(int $staleDays = 7): array
    {
        $stale = DB::table('todos')
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('updated_at', '<', now()->subDays($staleDays))
            ->count();

        $active = DB::table('todos')
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        return [
            'stale_tasks' => $stale,
            'active_tasks' => $active,
            'procrastination_ratio' => $active > 0
                ? round($stale / $active, 2)
                : null,
        ];
    }

    /**
     * How fast are tasks being completed?
     */
    public static function completionVelocity(): array
    {
        $last7Days = DB::table('todos')
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        $previous7Days = DB::table('todos')
            ->where('status', 'completed')
            ->whereBetween('updated_at', [
                now()->subDays(14),
                now()->subDays(7),
            ])
            ->count();

        $delta = $last7Days - $previous7Days;

        return [
            'completed_last_7_days' => $last7Days,
            'completed_previous_7_days' => $previous7Days,
            'trend' => $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat'),
            'delta' => $delta,
        ];
    }

    /**
     * Category + priority intelligence
     */
    public static function distribution(): array
    {
        return [
            'by_category' => DB::table('todos')
                ->select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->pluck('total', 'category'),

            'by_priority' => DB::table('todos')
                ->select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->pluck('total', 'priority'),
        ];
    }

    public static function overview(): array
    {
        return [
            'today' => self::today(),
            'week' => self::thisWeek(),
            'procrastination' => self::procrastination(),
            'velocity' => self::completionVelocity(),
            'distribution' => self::distribution(),
        ];
    }
}