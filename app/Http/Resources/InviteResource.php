<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InviteResource extends JsonResource
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
            'owner'      => UserMinimalResource::make($this->owner),
            'user'       => UserMinimalResource::make($this->user),
            'inviteable' => $this->getResponseModel(),
            'status'     => $this->status,
            'code'       => $this->code,
            'type'       => $this->type(),
            'created_at' => $this->created_at->timestamp
        ];
    }
}
