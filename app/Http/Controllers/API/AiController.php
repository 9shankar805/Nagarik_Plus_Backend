<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private AiService $aiService) {}

    /**
     * Send message and get AI response
     */
    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string|min:2|max:500',
        ]);

        $response = $this->aiService->chat($request->user(), $data['message']);

        return response()->json([
            'success' => true,
            'data'    => [
                'message'  => $response['message'],
                'intent'   => $response['intent'],
                'language' => $response['language'],
            ],
        ]);
    }

    /**
     * Get suggestion prompts
     */
    public function suggestions(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'en' => [
                    'How do I apply for a passport?',
                    'What documents are needed for PAN card?',
                    'How to renew my driving license?',
                    'How to register a company in Nepal?',
                    'Where is the nearest passport office?',
                    'What is the process for National ID?',
                    'How to register for voter ID?',
                    'Emergency numbers in Nepal',
                ],
                'np' => [
                    'राहदानी कसरी बनाउने?',
                    'प्यान कार्डका लागि के-के कागजात चाहिन्छ?',
                    'सवारी चालक अनुमतिपत्र कसरी नवीकरण गर्ने?',
                    'कम्पनी दर्ता कसरी गर्ने?',
                ],
            ],
        ]);
    }

    /**
     * Get user's conversation history
     */
    public function history(Request $request): JsonResponse
    {
        $history = AiConversation::where('user_id', $request->user()->id)
                                  ->orderByDesc('created_at')
                                  ->limit(50)
                                  ->get()
                                  ->map(fn($c) => [
                                      'id'           => $c->id,
                                      'user_message' => $c->user_message,
                                      'ai_response'  => $c->ai_response,
                                      'intent'       => $c->intent,
                                      'created_at'   => $c->created_at->toDateTimeString(),
                                  ]);

        return response()->json(['success' => true, 'data' => $history]);
    }
}
