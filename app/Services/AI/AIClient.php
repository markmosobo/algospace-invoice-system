<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class AIClient
{
    public static function ask(string $prompt): array
    {
        // 1️⃣ Create prediction
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

        if (!isset($response['id'])) {
            return self::fallback();
        }

        $predictionId = $response['id'];

        // 2️⃣ Poll until completed
        for ($i = 0; $i < 15; $i++) {
            sleep(1);

            $statusResponse = Http::withToken(config('services.replicate.token'))
                ->get("https://api.replicate.com/v1/predictions/{$predictionId}")
                ->json();

            if (($statusResponse['status'] ?? '') === 'succeeded') {
                $text = self::extractText($statusResponse);

                return [
                    'content' => $text ?: 'No meaningful output generated.',
                    'confidence' => self::confidenceFromText($text),
                    'model' => 'llama-3-70b',
                ];
            }

            if (($statusResponse['status'] ?? '') === 'failed') {
                break;
            }
        }

        return self::fallback();
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

    protected static function fallback(): array
    {
        return [
            'content' => 'I could not generate a confident answer at this time.',
            'confidence' => 0.3,
            'model' => 'llama-3-70b',
        ];
    }
}