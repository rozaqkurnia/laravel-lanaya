<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'body'          => $this->body,
            'category'      => $this->category->name,
            'status'        => $this->status,
            'comments_count'    => $this->comments->count(),
            'path'              => $this->path,
            'published_at'      => $this->published_at->diffForHumans(),
            'created_at'        => $this->created_at->diffForHumans(),
            'updated_at'        => $this->updated_at->diffForHumans(),
            'user'              => new UserResource($this->user)
        ];
    }
}
