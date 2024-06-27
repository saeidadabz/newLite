<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMinimalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'name'                    => $this->name,
            'username'                => $this->username,
            'avatar'                  => FileResource::make($this->avatar),
            'voice_status'            => $this->voice_status,
            'video_status'            => $this->video_status,
            'coordinates'             => $this->coordinates,
            'screenshare_coordinates' => $this->screenshare_coordinates,
            'screenshare_size'        => $this->screenshare_size,
            'video_coordinates'       => $this->video_coordinates,
            'video_size'              => $this->video_size,


        ];
    }
}
