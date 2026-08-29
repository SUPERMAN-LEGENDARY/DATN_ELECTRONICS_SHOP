<?php

namespace App\Http\Controllers;

use App\Services\GeminiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    public function send(Request $request, GeminiChatService $chatService)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $sessionToken = $request->input('session_token') ?: $request->session()->getId();

        $result = $chatService->handle($sessionToken, $request->input('message'));

        return response()->json($result);
    }
}