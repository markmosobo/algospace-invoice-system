<?php

namespace App\Services\AI;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiarySentimentService
{
    protected static array $positiveWords = [
        'good', 'great', 'progress', 'win', 'happy', 'grateful',
        'focused', 'productive', 'calm', 'clear', 'hopeful'
    ];

    protected static array $negativeWords = [
        'tired', 'stress', 'stressed', 'angry', 'frustrated',
        'overwhelmed', 'anxious', 'worried', 'burnt', 'burned',
        'lazy', 'stuck', 'confused'
    ];

    /**
     * Last N days sentiment analysis
     */
    public static function lastDays(int $days = 14): array
    {
        $entries = DB::table('diary_entries')
            ->where('created_at', '>=', now()->subDays($days))
            ->select('description', 'title', 'created_at')
            ->get();

        $positive = 0;
        $negative = 0;
        $neutral  = 0;

        foreach ($entries as $entry) {
            $text = $entry->description ?? $entry->title ?? '';
            $score = self::scoreText($text);

            if ($score > 0) {
                $positive++;
            } elseif ($score < 0) {
                $negative++;
            } else {
                $neutral++;
            }
        }

        $total = $entries->count();

        return [
            'period_days' => $days,
            'total_entries' => $total,
            'positive_entries' => $positive,
            'negative_entries' => $negative,
            'neutral_entries' => $neutral,
            'dominant_mood' => self::dominantMood($positive, $negative, $neutral),
        ];
    }

    /**
     * Detect stress signals in recent entries
     */
    public static function stressSignals(int $days = 7): array
    {
        $entries = DB::table('diary_entries')
            ->where('created_at', '>=', now()->subDays($days))
            ->select('description', 'title')
            ->get();

        $stressMentions = 0;

        foreach ($entries as $entry) {
            $text = $entry->description ?? $entry->title ?? '';
            if (Str::contains(
                Str::lower($text),
                ['stress', 'tired', 'overwhelmed', 'burnt', 'exhausted']
            )) {
                $stressMentions++;
            }
        }

        return [
            'period_days' => $days,
            'stress_mentions' => $stressMentions,
            'stress_signal' => $stressMentions >= 3 ? 'high' :
                               ($stressMentions >= 1 ? 'moderate' : 'low'),
        ];
    }

    /**
     * Mood trend: compare recent negative entries to previous period
     */
    public static function moodTrend(): array
    {
        $recent = self::lastDays(7);
        $previous = self::lastDays(14);

        $delta = $recent['negative_entries'] - $previous['negative_entries'];

        return [
            'trend' => $delta > 0 ? 'worsening' : ($delta < 0 ? 'improving' : 'stable'),
            'delta_negative_entries' => $delta,
        ];
    }

    /**
     * Overview for dashboard / AI insights
     */
    public static function overview(): array
    {
        return [
            'last_14_days' => self::lastDays(14),
            'stress' => self::stressSignals(7),
            'trend' => self::moodTrend(),
        ];
    }

    /**
     * Simple word-based sentiment scoring
     */
    protected static function scoreText(string $text): int
    {
        $text = Str::lower($text);
        $score = 0;

        foreach (self::$positiveWords as $word) {
            if (Str::contains($text, $word)) {
                $score++;
            }
        }

        foreach (self::$negativeWords as $word) {
            if (Str::contains($text, $word)) {
                $score--;
            }
        }

        return $score;
    }

    protected static function dominantMood(int $pos, int $neg, int $neu): string
    {
        return match (true) {
            $neg > $pos => 'negative',
            $pos > $neg => 'positive',
            default => 'neutral',
        };
    }
}