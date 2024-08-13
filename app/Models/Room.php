<?php

namespace App\Models;

use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\RoomServiceClient;
use Agence104\LiveKit\VideoGrant;
use App\Http\Resources\RoomResource;
use App\Http\Resources\UserResource;
use App\Utilities\Constants;
use App\Utilities\Settingable;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use Settingable;

    protected $fillable = [
        'title',
        'active',
        'is_private',
        'password',
        'status',
        'landing_spot',
        'workspace_id',
        'user_id'
    ];

    protected $appends = [
        'channel'
    ];

    public function mentionedBy()
    {
        return $this->title;
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function background()
    {
        return $this->files->where('type', 'background')->last();
    }


    public function isDirectRoom()
    {
        return $this->workspace_id === NULL;
    }

    public function participants()
    {
        if ($this->workspace_id === NULL) {
            return User::find(explode('-', $this->title));

        }

        return $this->users;


    }

    public function logo()
    {
        return $this->files->where('type', 'logo')->last();
    }

    public function getChannelAttribute($value)
    {
        return 'room-' . $this->id;

    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seens()
    {
        return $this->hasMany(Seen::class);
    }

    public function unseens($user)
    {

        return $this->messages()->count() - $this->seens()->whereUserId($user->id)->count();

    }

    public function joinUser($user, $joinLivekit = TRUE)
    {
        $workspace = $this->workspace;
//        $workspace = $user->workspaces->find($workspace->id);
//        if ($workspace === NULL) {
//            return error('You have no access to this workspace');
//
//        }


        $user->update([
            'room_id' => $this->id,
            'workspace_id' => $workspace->id,
        ]);


        if ($joinLivekit) {
            $roomName = $this->id;
            $participantName = $user->username;

            $tokenOptions = (new AccessTokenOptions())
                ->setIdentity($participantName)->setTtl(99999);
//
//            $videoGrant = (new VideoGrant())
//                ->setRoomJoin()
//                ->setRoomName($roomName);

            $token = (new AccessToken('devkey', 'secret'))
                ->init($tokenOptions)
//                ->setGrant($videoGrant)
                ->toJwt();

            //TODO: Socket, user joined to room.

            $this->token = $token;
        }


        return $this;


    }


    public function lkUsers()
    {
//        return [];
        $host = 'https://live-kit-server.cotopia.social';
        $svc = new RoomServiceClient($host, 'devkey', 'secret');
        return $svc->listParticipants($this->id)->getParticipants()->getIterator();


    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
