<?php

namespace App\Service;

use App\Models\Reply;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ReplyService
{

    //khai bao model
    protected $model;

    //tạo constructor, khởi tạo
    public function __construct(Reply $reply)
    {
        $this->model = $reply;
    }

    //add
    public function createReply($params)
    {
        try {
            $result = $this->model->create([
                'user_id' => auth()->user()->id,
                'comment_id' => $params['comment_id'],
                'content' => $params['content'],
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(
                [
                    'message' => 'Create comment fail'
                ],
                400
            );
        }

        return response()->json([
            'message' => 'Create comment Successful',
            'data' => $result
        ], 200);
    }

    //update
    public function updateReply($reply, $params)
    {
        try {
            $user_reply = User::findOrFail($reply->user_id);

            $user_token = auth()->user();

            if ($user_reply->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not the owner of this reply'
                ], 403);
            }
        } catch (ModelNotFoundException $e) {
            Log::error(': ' . $e->getMessage());

            return response()->json([
                'message' => 'You are not the owner of this reply',
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

            $result = $reply->update($params->all());

            if ($result) {
                return response()->json([
                    'message' => 'Update successful',
                    'data' => $reply->fresh()  // Lấy dữ liệu mới nhất
                ], 200);
            }

            return response()->json([  // Thêm return
                'message' => 'Update unsuccessful'
            ], 400);
        } catch (Exception $exception) {
            Log::error('Update user error: ' . $exception->getMessage());
            return response()->json([
                'message' => 'Update unsuccessful',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}
