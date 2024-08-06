<?php

namespace App\Models;

use App\Utilities\Settingable;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{

    use Settingable;

    protected $fillable = [
        'title',
        'description',
        'active',
        'is_private'
    ];

    protected $appends = [
        'channel'
    ];

    public function logo()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function hasUser($user)
    {
        return $this->users->contains($user->id);
    }

    // TODO - This should be deleted because assigning roles must be done by admins not by just joining to a workspace. Actually if the user has member permission should be able to join to any workspace.
    public function joinUser($user, $role = 'member', $tag = NULL)
    {
        if (! $this->users->contains($user->id)) {
            $this->users()->attach($user, ['role' => $role, 'tag_id' => $tag]);
            $user->update([
                'workspace_id' => $this->id
            ]);
//            $user->giveRole($role, $this);
            //TODO: Socket, user joined to ws.

//            $user->giveRole($role, $this);

        }
        return $this;

    }

    public function mentionedBy()
    {
        return $this->title;
    }

    public function getChannelAttribute($value)
    {
        return 'workspace-'.$this->id;

    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
