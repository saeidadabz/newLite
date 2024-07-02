<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'active',
        'status',
        'bio',
        'workspace_id',
        'room_id',
        'voice_status',
        'video_status',
        'coordinates',
        'screenshare_coordinates',
        'screenshare_size',
        'video_coordinates',
        'video_size',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }


    public function avatar()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class)->withPivot('role', 'tag_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function activites()
    {
        return $this->hasMany(Activity::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class)->withPivot('role');
    }

    public function directs()
    {
        return Room::where('title', 'regexp', "[[:<:]]$this->id[[:>:]]")->get();
    }

    public function giveRole($ability, $workspace)
    {
        $permissions = Role::ROLES[$ability];
        $currentToken = auth()->user()->currentAccessToken();
        $abilities = $currentToken->abilities;

        foreach ($permissions as $permission) {
            $abilities[] = $permission . '-' . $workspace->id;

        }

        $currentToken->abilities = $abilities;
        $currentToken->save();
    }
}
