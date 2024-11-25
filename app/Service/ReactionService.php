<?php

namespace App\Service;

use App\Models\Reaction;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ReactionService
{

    //khai bao model
    protected $model;

    //tạo constructor, khởi tạo
    public function __construct(Reaction $reaction)
    {
        $this->model = $reaction;
    }

    //like and cancel
    public function likeReaction($params)
    {
        try {
            $userId = auth()->user()->id;
            $blogId = $params['blog_id'];

            $reaction = Reaction::where([
                'user_id' => $userId,
                'blog_id' => $blogId
            ])->first();

            if ($reaction) {
                // Xử lý reaction hiện có
                $newStatus = $reaction->reactionType == true ? null : true;
                $reaction->update(['reactionType' => $newStatus]);

                $message = $newStatus == true ? 'Liked blog successfully' : 'Cancelled like successfully';

                return response()->json([
                    'message' => $message,
                    'data' => $reaction->refresh()
                ], 200);
            }

            // Tạo reaction mới nếu chưa tồn tại
            $reaction = $this->model->create([
                'user_id' => $userId,
                'blog_id' => $blogId,
                'reactionType' => true
            ]);

            return response()->json([
                'message' => 'Liked blog successfully',
                'data' => $reaction
            ], 200);
        } catch (Exception $e) {
            Log::error('Like Reaction Error:', [
                'error' => $e->getMessage(),
                'user_id' => $userId ?? null,
                'blog_id' => $blogId ?? null
            ]);

            return response()->json([
                'message' => 'Failed to process reaction',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 400);
        }
    }

    //dislike and cancel
    public function dislikeReaction($params)
    {
        try {
            $userId = auth()->id();
            $blogId = $params['blog_id'];

            $reaction = Reaction::where([
                'user_id' => $userId,
                'blog_id' => $blogId
            ])->first();

            if ($reaction) {
                // Xử lý reaction hiện có
                $newStatus = $reaction->reactionType === false ? null : false;
                $reaction->update(['reactionType' => $newStatus]);

                $message = $newStatus === false ? 'Disliked blog successfully' : 'Cancelled dislike successfully';

                return response()->json([
                    'message' => $message,
                    'data' => $reaction->refresh(),
                    'is_disliked' => $newStatus === false
                ], 200);
            }

            // Tạo reaction mới nếu chưa tồn tại
            $reaction = $this->model->create([
                'user_id' => $userId,
                'blog_id' => $blogId,
                'reactionType' => false // Set false for dislike
            ]);

            return response()->json([
                'message' => 'Disliked blog successfully',
                'data' => $reaction,
                'is_disliked' => true
            ], 200);
        } catch (Exception $e) {
            Log::error('Dislike Reaction Error:', [
                'error' => $e->getMessage(),
                'user_id' => $userId ?? null,
                'blog_id' => $blogId ?? null
            ]);

            return response()->json([
                'message' => 'Failed to process reaction',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 400);
        }
    }
}
