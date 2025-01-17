<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'         => (int) $this->id,
            'topic_id'   => (int) $this->topic_id,
            'user_id'    => (int) $this->user_id,
            'content'    => (string) $this->content,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'user'       => new UserResource($this->whenLoaded('user')),
            'topic'      => new TopicResource($this->whenLoaded('topic')),
        ];
    }
}
