<?php

namespace App\Service;

use App\Models\Comment;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CommentService
{

    //khai bao model
    protected $model;

    //tạo constructor, khởi tạo
    public function __construct(Comment $comment)
    {
        $this->model = $comment;
    }

    //add
    public function createComment($params)
    {
        try {
            $result = $this->model->create([
                'user_id' => auth()->user()->id,
                'blog_id' => $params['blog_id'],
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
    public function updateComment($comment, $params)
    {
        try {
            $user_comment = User::findOrFail($comment->user_id);

            $user_token = auth()->user();

            if ($user_comment->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not the owner of this comment'
                ], 403);
            }
        } catch (ModelNotFoundException $e) {
            Log::error(': ' . $e->getMessage());

            return response()->json([
                'message' => 'You are not the owner of this comment',
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

            $result = $comment->update($params->all());

            if ($result) {
                return response()->json([
                    'message' => 'Update successful',
                    'data' => $comment->fresh()  // Lấy dữ liệu mới nhất
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
