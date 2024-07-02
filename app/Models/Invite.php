<?php

namespace App\Models;

use App\Http\Resources\JobResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\WorkspaceResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'owner_id',
        'user_id',
        'status',
        'inviteable_type',
        'inviteable_id'
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


    public function getResponseModel()
    {
        if ($this->inviteable() instanceof Workspace) {

            return WorkspaceResource::make($this->inviteable);
        }
        if ($this->inviteable() instanceof Job) {

            return JobResource::make($this->inviteable);
        }
        if ($this->inviteable() instanceof Room) {

            return RoomResource::make($this->inviteable);
        }
    }

    public function inviteable()
    {
        return $this->morphTo();
    }

}
