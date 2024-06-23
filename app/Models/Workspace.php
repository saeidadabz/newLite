<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{

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

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function joinUser($user, $role = 'member')
    {
        if (!$this->users->contains($user->id)) {
            $this->users()->attach($user, ['role' => $role]);
            $user->update([
                              'workspace_id',
                              $this->id
                          ]);

        }

    }

    public function getChannelAttribute($value)
    {
        return 'workspace-' . $this->id;

    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}
