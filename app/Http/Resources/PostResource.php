<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $timeAgo = $this->created_at->diffForHumans();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->images->first() ? $this->images->first()->image : null,
            'created_at' => $timeAgo,
            'author' => new PostWithAuthorResource($this->user),
        ];
    }
}
