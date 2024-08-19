<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {

        return [
            'id'         => $this->id,
            'user'       => UserMinimalResource::make($this->user),
            'text'       => $this->text,
            'files'      => FileResource::collection($this->files),
            'room_id'    => $this->room_id,
            'seen'       => FALSE,
            //TODO: has to have another req for seens
            'is_edited'  => $this->is_edited,
            'is_pinned'  => $this->is_pinned,
            'reply_to'   => $this->reply_to,
            'mentions'   => $this->mentions,
            'links'      => $this->links,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at?->timestamp,

        ];
    }
}
