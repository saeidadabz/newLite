<?php

namespace App\Models;

use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'title',
        'active',
        'is_private',
        'password',
        'status',
        'landing_spot',
        'workspace_id',
        'user_id',
    ];

    protected $appends = [
        'channel',
    ];

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

    public function participants()
    {
        if ($this->workspace_id === null) {
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
        return 'room-'.$this->id;

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

        return $this->messages()->whereUserId($user->id)->count() - $this->seens()->whereUserId($user->id)->count();

    }

    public function joinUser($user)
    {
        $workspace = $this->workspace;
        $workspace = $user->workspaces->find($workspace->id);
        if ($workspace === null) {
            return error('You have no access to this workspace');

        }

        $user->update([
            'room_id' => $this->id,
        ]);

        $roomName = $this->id;
        $participantName = $user->username;

        $tokenOptions = (new AccessTokenOptions())
            ->setIdentity($participantName);

        $videoGrant = (new VideoGrant())
            ->setRoomJoin()
            ->setRoomName($roomName);

        $token = (new AccessToken('devkey', 'secret'))
            ->init($tokenOptions)
            ->setGrant($videoGrant)
            ->toJwt();

        //TODO: Socket, user joined to room.

        $this->token = $token;

        return $this;

    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
