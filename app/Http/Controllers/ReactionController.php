<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reaction\LikeRequest;
use App\Service\ReactionService;
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


}
