<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Message;
use Illuminate\Http\Request;
// use GeminiAPI\Laravel\Facades\Gemini;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    public function ChatWithGemini(Request $request, $blogId)
    {
        try {
            $user = auth()->user();

            if (is_null($user->upgrade_at)) {
                return response()->json([
                    'message' => 'Please upgrade to a premium account to use premium feature (chat with AI). '
                ], 403);
            }
            
            $request->validate([
                'message' => 'required|string',
            ]);
            // Get user's message
            $userInput = $request->input('message');

            // Get blog 
            $blog = Blog::findOrFail($blogId);
            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
            }

            $firstShot = "You are a highly informative and helpful chatbot assistant. Your primary function is to provide comprehensive and informative responses to user queries based solely on the provided blog content. You will strictly adhere to the information presented in the blog and avoid making any claims or providing information that is not directly supported by the text. Please provide responses that are concise, relevant, and easy to understand.
            Here is the content:
            " . $blog->content;


            // Fetch conversation history for the blog
            $messages = Message::where('blog_id', $blogId)
                ->where('user_id', auth()->user()->id)
                ->orderBy('created_at')
                ->get();


            $history = [];

            // Thêm initial prompt vào đầu tiên
            $history[] = Content::parse(
                part: $firstShot,
                role: Role::USER,
            );
            
            // Thêm các tin nhắn còn lại
            foreach ($messages as $message) {
                $history[] = Content::parse(
                    part: $message->content,
                    role: $message->role === 'user' ? Role::USER : Role::MODEL,
                );
            }
            
            $chat = Gemini::chat()
                ->startChat(history: $history);

         
            $response = $chat->sendMessage($userInput);

            // Save the user's message and AI's response to the database
            Message::create([
                'user_id' => auth()->id(), // Assuming authenticated user
                'blog_id' => $blogId,
                'content' => $userInput,
                'role' => 'user',
            ]);
            $result = Message::create([
                'user_id' => auth()->id(),
                'blog_id' => $blogId,
                'content' => $response->text(),
                'role' => 'model',
            ]);

            return response()->json([
                'data' => $result,
                'message' => 'Successfull'
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
    public function GetChatHistory($blogId) {
        try {
            $result = Message::where('blog_id', $blogId)
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at')
            ->get();

            return response()->json([
                'message' => 'Get chat history Successful',
                'data' => $result
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Blog chat history not found!'
            ], 404);
        }
    }
}