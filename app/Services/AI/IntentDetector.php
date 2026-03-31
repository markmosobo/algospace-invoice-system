<?php 

namespace App\Services\AI;

use Illuminate\Support\Str;

class IntentDetector
{
    public static function detect(string $message): string
    {
        $message = Str::lower($message);

        return match (true) {
            Str::contains($message, ['predict', 'forecast', 'next month']) => 'projection',
            Str::contains($message, ['why', 'reason', 'explain']) => 'insight',
            Str::contains($message, ['todo', 'task', 'do today']) => 'todo_feedback',
            Str::contains($message, ['mood', 'diary', 'feel']) => 'diary_analysis',
            default => 'general'
        };
    }
}