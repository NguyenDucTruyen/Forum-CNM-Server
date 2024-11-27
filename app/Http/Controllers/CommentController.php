<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\AddRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use App\Service\BlogService;
use App\Service\CommentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    protected $service;

    //tạo constructor
    public function __construct(CommentService $commentService)
    {
        $this->service = $commentService;
    }

    //add
    public function createComment(AddRequest $addRequest)
    {
        $params = $addRequest->validated();

        $result = $this->service->createComment($params);

        return $result;
    }

    //update
    public function updateComment(UpdateRequest $updateRequest, $id)
    {
        $params = $updateRequest;

        try {
            $comment = Comment::findOrFail($id);

            $result = $this->service->updateComment($comment, $params);
            return $result;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Comment not found!'
            ], 404);
        }
    }

    //delete
    /**
     * Xóa mềm comment
     */
    public function destroyComment($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            $user_Comment = User::findOrFail($comment->user_id);

            $user_token = auth()->user();

            if ($user_token->role != 'admin' && $user_Comment->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not permission of this Comment'
                ], 403);
            }
        } catch (ModelNotFoundException $e) {
            Log::error(': ' . $e->getMessage());

            return response()->json([
                'message' => 'You are not the owner of this Comment',
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
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment has deleted'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Comment not found!'
            ], 404);
        }
    }

    //get by id blog include reply 
    public function listCommentBlog($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $comments_replies = $blog->comments()->with('replies')->get();

            return response()->json([
                'message' => 'success',
                'data' => $comments_replies
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Blog not found!'
            ], 404);
        }
    }

}
