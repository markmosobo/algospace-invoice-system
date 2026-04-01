<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class AIClient
{
    public static function ask(string $prompt): array
    {
        $response = Http::withToken(config('services.replicate.token'))
            ->post('https://api.replicate.com/v1/predictions', [
                'version' => config('services.replicate.llama_70b'),
                'input' => [
                    'prompt' => $prompt,
                    'max_tokens' => 300,
                    'temperature' => 0.6,
                ],
            ])
            ->json();

        $text = self::extractText($response);

        return [
            'content' => $text ?: 'I could not generate a confident answer.',
            'confidence' => self::confidenceFromText($text),
            'model' => 'llama-3-70b',
        ];
    }

    protected static function extractText(array $response): ?string
    {
        if (!isset($response['output'])) {
            return null;
        }

        return trim(collect($response['output'])->implode(''));
    }

    protected static function confidenceFromText(?string $text): float
    {
        $len = strlen($text ?? '');

        return match (true) {
            $len > 400 => 0.9,
            $len > 200 => 0.8,
            $len > 100 => 0.65,
            default => 0.45,
        };
    }
}