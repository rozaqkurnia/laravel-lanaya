<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments;

        $response = [
            'comments' => $comments
        ];
        
        if ($comments->isEmpty()) {
            $response['message'] = 'No comments available';
            return response($response, Response::HTTP_OK);
        }

        $response['comments'] = CommentResource::collection($comments);
        return response($response, Response::HTTP_OK);
    }

    public function store(Post $post, CommentRequest $request)
    {
        $comment = $post->comments()->create([
            'body'      => $request->body,
            'user_id'   => 1
        ]);

        $response = [
            'comment' => new CommentResource($comment)
        ];

        return response($response, Response::HTTP_CREATED);
    }

    public function show(Post $post, Comment $comment)
    {
        $response = [
            'comment' => new CommentResource($comment)
        ];
        return response($response, Response::HTTP_OK);
    }

    public function update(Post $post, CommentRequest $request, Comment $comment)
    {
        $comment->update([
            'body' => $request->body
        ]);

        $comment->save();

        $response = [
            'comment' => new CommentResource($comment)
        ];

        return response($response, Response::HTTP_ACCEPTED);
    }

    public function destroy(Post $post, Comment $comment)
    {
        $comment->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
