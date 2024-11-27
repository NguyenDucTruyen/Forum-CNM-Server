<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reaction\LikeRequest;
use App\Models\Blog;
use App\Service\ReactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    protected $service;

    //tạo constructor
    public function __construct(ReactionService $reactionService)
    {
        $this->service = $reactionService;
    }

    //like
    public function likeReaction(LikeRequest $likeRequest)
    {
        $params = $likeRequest->validated();

        $result = $this->service->likeReaction($params);

        return $result;
    }

    //dislike
    public function dislikeReaction(LikeRequest $likeRequest)
    {
        $params = $likeRequest->validated();

        $result = $this->service->dislikeReaction($params);

        return $result;
    }

    //get list reaction by blog ID

    public function listReactionBlog($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $reacts = $blog->reactions;

            // Đếm số lượng reactionType = true
        $likeCount = $reacts->where('reactionType', true)->count();

        // Đếm số lượng reactionType = true
        $dislikeCount = $reacts->where('reactionType', false)->count();

            return response()->json([
                'message' => 'success',
                'total'=>$reacts->count(),
                'like'=>$likeCount,
                'dislike'=>$dislikeCount,
                'data' => $reacts
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Blog not found!'
            ], 404);
        }
    }
}
