<?php

namespace App\Models;

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
        'user_id'
    ];

    protected $appends = [
        'channel'
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
        return $this->files->where('type', 'background')->first();
    }

    public function logo()
    {
        return $this->files->where('type', 'logo')->first();
    }

    public function getChannelAttribute($value)
    {
        return 'room-' . $this->id;

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
