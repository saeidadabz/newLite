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
        return $this->belongsToMany(Workspace::class)->withPivot('role');
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


    public function giveRole($ability)
    {
        $currentToken = $this->currentAccessToken();
        $abilities = $currentToken->abilities;
        $abilities[] = 'get-' . $workspace->id;
        $currentToken->abilities = $abilities;
        $currentToken->save();
    }
}
