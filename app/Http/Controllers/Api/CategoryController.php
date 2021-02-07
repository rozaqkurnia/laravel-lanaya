<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        $response = [
            'categories'    => $categories,
        ];

        if ($categories->isEmpty()) {
            $data['message'] = 'no category available.';
            return response($data, Response::HTTP_OK);
        }

        $response['categories'] = CategoryResource::collection($categories);

        return response($response, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        $response = [
            'category' => new CategoryResource($category)
        ];

        return response($response, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category   = Category::find($category)->first();
        $response       = [
            'category'  => new CategoryResource($category)
        ];
        return response($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category = Category::find($category)->first();

        $name       = $request->name;
        $slug       = Str::slug($name);

        $category->update([
            'name' => $name,
            'slug' => $slug
        ]);

        $category->save();

        $response = ['category' => new CategoryResource($category)];

        return response($response, Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category = Category::find($category)->first();

        $category->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
