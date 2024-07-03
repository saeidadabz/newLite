<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'token'        => $this->token,
            'is_private'   => $this->is_private,
            'workspace_id' => $this->workspace_id,
            'participants' => UserMinimalResource::collection($this->participants()),
            'landing_spot' => $this->landing_spot,
            'background'   => FileResource::make($this->background()),
            'logo'         => FileResource::make($this->logo()),
            'unseens'      => $this->unseens(auth()->user()),
        ];
    }
}
