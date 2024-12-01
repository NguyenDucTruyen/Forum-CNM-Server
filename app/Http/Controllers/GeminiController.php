<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    public function generateText(Request $request)
    {
        try {
            $prompt = $request->input('prompt');

            $response = Gemini::geminiPro()
                ->generateContent($prompt);

            return response()->json([
                'text' => $response->text()
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Model not found!'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }
}