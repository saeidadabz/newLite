<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'owner_id',
        'user_id',
        'workspace_id',
        'room_id',
        'status'
    ];

    protected $appends = ['code'];

    public function getCodeAttribute($value)
    {
        return base_convert(10000000 - $this->id, 10, 36);

    }

    public static function findByCode($value)
    {
        return self::findOrFail(10000000 - base_convert($value, 36, 10));

    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
