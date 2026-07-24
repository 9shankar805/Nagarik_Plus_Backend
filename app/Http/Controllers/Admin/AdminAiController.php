<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use Illuminate\Http\Request;

class AdminAiController extends Controller
{
    public function logs()
    {
        $conversations = AiConversation::with('user')->latest()->paginate(20);
        return view('admin.ai.logs', compact('conversations'));
    }

    public function suggestions()
    {
        $suggestions = [
            ['id' => 1, 'text' => 'How do I apply for a passport?', 'category' => 'Passport'],
            ['id' => 2, 'text' => 'What documents are needed for PAN card?', 'category' => 'Tax'],
            ['id' => 3, 'text' => 'How to renew my driving license?', 'category' => 'Transport'],
            ['id' => 4, 'text' => 'Where is the nearest passport office?', 'category' => 'Office'],
        ];
        return view('admin.ai.suggestions', compact('suggestions'));
    }
}
