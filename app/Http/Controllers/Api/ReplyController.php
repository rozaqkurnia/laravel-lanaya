<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReplyResource;
use App\Http\Requests\ReplyRequest;
use App\Models\Comment;
use App\Models\Reply;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReplyController extends Controller
{
    public function index(Comment $comment)
    {
        $replies = $comment->replies;
        $response = [
            'replies' => $replies
        ];
        
        if ($replies->isEmpty()) {
            $response['message'] = 'No reply available';
            return response($response, Response::HTTP_OK);
        }

        $response['replies'] = ReplyResource::collection($replies);
        return response($response, Response::HTTP_OK);
    }

    public function store(Comment $comment, ReplyRequest $request)
    {
        $reply = $comment->replies()->create([
            'body'      => $request->body,
            'user_id'   => 1
        ]);

        $response = [
            'reply' => new ReplyResource($reply)
        ];

        return response($response, Response::HTTP_CREATED);
    }

    public function show(Comment $comment, Reply $reply)
    {
        $response = [
            'reply' => new ReplyResource($reply)
        ];

        return response($response, Response::HTTP_OK);
    }

    public function update(Comment $comment, ReplyRequest $request, Reply $reply)
    {
        $reply->update([
            'body' => $request->body
        ]);

        $reply->save();

        $response = [
            'reply' => new ReplyResource($reply)
        ];

        return response($response, Response::HTTP_ACCEPTED);
    }

    public function destroy(Comment $comment, Reply $reply)
    {
        $reply->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
