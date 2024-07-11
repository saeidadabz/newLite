<?php

namespace App\Models;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'owner_id',
        'workspace_id',
    ];

    protected $casts = [
        'owner_id'     => 'int',
        'workspace_id' => 'int',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function canUserAccess(User $user)
    {
        $ownership = intval($user->id) === $this->owner_id;

        return $ownership || $this->workspace->hasUser($user) || $user->tokenCan(PermissionEnum::CALENDAR_VIEW->value);
    }
}
