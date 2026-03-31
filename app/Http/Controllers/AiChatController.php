<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AiChatSession;
use App\Models\AiChatMessage;
use App\Services\AI\IntentDetector;
use App\Services\AI\PromptBuilder;
use App\Services\AI\AIClient;
use App\Services\AI\DataHydrator;

class AiChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|exists:ai_chat_sessions,id',
        ]);

        $user = $request->user();

        /** 1️⃣ Resolve or create session */
        $session = $request->session_id
            ? AiChatSession::find($request->session_id)
            : AiChatSession::create([
                'user_id' => $user->id,
                'title' => Str::limit($request->message, 40)
            ]);

        /** 2️⃣ Store user message */
        $userMessage = AiChatMessage::create([
            'session_id' => $session->id,
            'role' => 'user',
            'content' => $request->message,
        ]);

        /** 3️⃣ Detect intent */
        $intent = IntentDetector::detect($request->message);

        /** 4️⃣ Hydrate system data */
        $dataContext = DataHydrator::hydrate($intent);

        /** 5️⃣ Build prompt */
        $prompt = PromptBuilder::build(
            userMessage: $request->message,
            intent: $intent,
            context: $dataContext
        );

        /** 6️⃣ Call AI */
        $aiResponse = AIClient::ask($prompt);

        /** 7️⃣ Store AI message */
        $aiMessage = AiChatMessage::create([
            'session_id' => $session->id,
            'role' => 'ai',
            'content' => $aiResponse['content'],
            'confidence_score' => $aiResponse['confidence'] ?? null,
            'meta' => [
                'intent' => $intent,
                'model' => $aiResponse['model'] ?? null,
            ],
        ]);

        return response()->json([
            'session_id' => $session->id,
            'message' => $aiMessage,
        ]);
    }

    public function messages(AiChatSession $session)
    {
        $this->authorize('view', $session);

        return $session->messages()->orderBy('created_at')->get();
    }
}