<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user'       => UserMinimalResource::make($this->user),
            'text'       => $this->text,
            'files'      => FileResource::collection($this->files),
            'room'       => RoomResource::make($this->room),
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
            'seen'       => $this->saw(auth()->user()),
        ];
    }
}
