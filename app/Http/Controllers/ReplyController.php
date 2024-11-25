<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reply\AddRequest;
use App\Http\Requests\Reply\Updaterequest;
use App\Models\Reply;
use App\Models\User;
use App\Service\ReplyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReplyController extends Controller
{
    protected $service;

    //tạo constructor
    public function __construct(ReplyService $replyService)
    {
        $this->service = $replyService;
    }

    //add
    public function createReply(AddRequest $addRequest)
    {
        $params = $addRequest->validated();

        $result = $this->service->createReply($params);

        return $result;
    }

    //update
    public function updateReply(Updaterequest $updateRequest, $id)
    {
        $params = $updateRequest;

        try {
            $reply = Reply::findOrFail($id);

            $result = $this->service->updateReply($reply, $params);
            return $result;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Reply not found!'
            ], 404);
        }
    }

    //delete
    /**
     * Xóa mềm comment
     */
    public function destroyReply($id)
    {
        try {
            $reply = Reply::findOrFail($id);

            $user_Reply = User::findOrFail($reply->user_id);

            $user_token = auth()->user();

            if ($user_token->role != 'admin' && $user_Reply->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not permission of this Reply'
                ], 403);
            }
        } catch (ModelNotFoundException $e) {
            Log::error(': ' . $e->getMessage());

            return response()->json([
                'message' => 'You are not the owner of this Reply',
                'error' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            // Xử lý lỗi khác
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $reply->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reply has deleted'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Reply not found!'
            ], 404);
        }
    }
}
