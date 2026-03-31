<?php

namespace App\Services\AI;

class DataHydrator
{
    public static function hydrate(string $intent): array
    {
        return match ($intent) {

            // WHY questions → diagnosis
            'insight' => [
                'finance' => FinancialSnapshotService::thisWeek(),
                'behavior' => TodoSnapshotService::thisWeek(),
                'mood' => DiarySentimentService::lastDays(7),
            ],

            // WHAT NEXT → coaching
            'todo_feedback' => [
                'today' => TodoSnapshotService::today(),
                'behavior' => TodoSnapshotService::overview(),
                'mood' => DiarySentimentService::stressSignals(7),
            ],

            // FUTURE → forecasting
            'projection' => [
                'finance_trend' => FinancialSnapshotService::last3Months(),
                'behavior_velocity' => TodoSnapshotService::completionVelocity(),
                'mood_trend' => DiarySentimentService::moodTrend(),
            ],

            // EMOTIONAL questions
            'diary_analysis' => [
                'mood' => DiarySentimentService::overview(),
                'behavior' => TodoSnapshotService::overview(),
            ],

            // DEFAULT → system awareness
            default => [
                'finance' => FinancialSnapshotService::overview(),
                'behavior' => TodoSnapshotService::overview(),
                'mood' => DiarySentimentService::overview(),
            ],
        };
    }
}