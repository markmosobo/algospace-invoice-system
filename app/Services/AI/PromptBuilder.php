<?php

namespace App\Services\AI;

class PromptBuilder
{
    public static function build(
        string $userMessage,
        string $intent,
        array $context
    ): string {
        // Format context as a string first
        $contextString = self::formatContext($context);

        return <<<PROMPT
You are a calm, analytical second-brain assistant.

User question:
"$userMessage"

Intent:
$intent

Context data (summarized & trusted):
$contextString

Instructions:
- Explain reasoning clearly
- Be concise but insightful
- Suggest actionable steps if relevant
- Admit uncertainty if data is incomplete
- Limit response to 150 words
PROMPT;
    }

    protected static function formatContext(array $context): string
    {
        return json_encode($context, JSON_PRETTY_PRINT);
    }
}