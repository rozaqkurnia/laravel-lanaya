<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function index()
    {
        $posts      = Post::latest()->get();
        
        $response = [
            'posts' => $posts
        ];

        if ($posts->isEmpty()) {
            $response['message'] = 'No post available';
            return response($response, Response::HTTP_OK);
        }

        $response['posts']   = PostResource::collection($posts);

        return response($response, Response::HTTP_OK);
    }

    public function store(PostRequest $request)
    {
        $title          = $request->title;
        $slug           = Str::slug($title);
        $category_id    = $request->category_id;
        $published      = $request->published;
        $published_at   = Carbon::now();

        // updated soon. need to configure user token first
        // $post = auth()->user()->posts()->create([
        //     'title'         => $title,
        //     'slug'          => $slug,
        //     'category_id'   => $category_id,
        //     'body'          => $request->body
        // ]);

        $post = Post::create([
            'uuid'          => Str::uuid(),
            'title'         => $title,
            'slug'          => $slug,
            'category_id'   => $category_id,
            'body'          => $request->body,
            'user_id'       => 1,
            'published'     => $published,
            'published_at'  => $published_at
        ]);

        $response = [
            'post' => new PostResource($post)
        ];

        return response($response, Response::HTTP_CREATED);
    }

    public function show(Post $post)
    {
        $response = [
            'post'  => new PostResource($post)
        ];
        return response($response, Response::HTTP_OK);
    }

    public function update(Post $post, PostRequest $request)
    {
        $data = [
            'title'         => $request->title,
            'slug'          => Str::slug($request->title),
            'category_id'   => $request->category_id,
            'body'          => $request->body,
            'published'     => $request->published
        ];

        if (!$post->published && ($request->published == 1)) {
            $data['published_at'] = Carbon::now();
        }

        $post->update($data);

        $response = [
            'data' => new PostResource($post)
        ];

        return response($response, Response::HTTP_ACCEPTED);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
