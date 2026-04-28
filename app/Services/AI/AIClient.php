<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIClient
{
    public static function ask(string $prompt): array
    {
        $client   = new Client();
        $apiToken = env('REPLICATE_API_TOKEN');

        try {
            /** 1️⃣ Send request to Replicate */
            $response = $client->post('https://api.replicate.com/v1/predictions', [
                'headers' => [
                    'Authorization' => "Token {$apiToken}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'version' => config('services.replicate.model'),
                    'input' => [
                        'prompt' => $prompt,
                        'temperature' => 0.7,
                        'max_new_tokens' => 500,
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            /** 2️⃣ Poll until completed */
            $getUrl = $body['urls']['get'] ?? null;
            if (!$getUrl) {
                throw new \Exception('Failed to initiate prediction');
            }

            for ($i = 0; $i < 12; $i++) {
                sleep(1);

                $pollResponse = $client->get($getUrl, [
                    'headers' => [
                        'Authorization' => "Token {$apiToken}",
                    ],
                ]);

                $pollBody = json_decode($pollResponse->getBody(), true);

                if ($pollBody['status'] === 'succeeded') {
                    return [
                        'content' => is_array($pollBody['output'])
                            ? implode('', $pollBody['output'])
                            : $pollBody['output'],
                        'model' => 'meta-llama-3-70b',
                        'confidence' => null,
                    ];
                }

                if ($pollBody['status'] === 'failed') {
                    throw new \Exception('Prediction failed');
                }
            }

            throw new \Exception('AI response timed out');

        } catch (\Exception $e) {
            Log::error('AIClient Error: ' . $e->getMessage());

            return [
                'content' => 'Sorry, I could not generate a response at the moment.',
                'model' => 'meta-llama-error',
                'confidence' => null,
            ];
        }
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