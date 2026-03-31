<?php

namespace App\Services\AI;

class AIClient
{
    public static function ask(string $prompt): array
    {
        // Placeholder – swap model later
        return [
            'content' => 'AI response goes here',
            'confidence' => 0.82,
            'model' => 'llama-3-70b',
        ];
    }
}