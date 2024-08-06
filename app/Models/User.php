<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Utilities\Constants;
use App\Utilities\Settingable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, Settingable;

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
        'is_megaphone',
        'socket_id',
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


    public static function byUsername($username)
    {
        return self::where('username', $username)->firstOrFail();

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

    public function activities()
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

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function directs()
    {
        return Room::where('title', 'regexp', "[[:<:]]$this->id[[:>:]]")->get();
    }


    public function checkIsInRoomForReal()
    {


        if ($this->room_id === NULL) {

        }

    }

    public function giveRole($ability, $workspace)
    {
        $permissions = Constants::ROLE_PERMISSIONS[$ability];
        $currentToken = auth()->user()->currentAccessToken();
        $abilities = $currentToken->abilities;

        foreach ($permissions as $permission) {
            $abilities[] = $permission . '-' . $workspace->id;

        }

        $currentToken->abilities = $abilities;
        $currentToken->save();
    }


    public function mentionedBy()
    {
        return $this->username;
    }

    public function isOwner($id): bool
    {
        return (int) $this->id === (int) $id;
    }

//    public function createToken(string $name, DateTimeInterface $expiresAt = NULL)
//    {
//        $plainTextToken = $this->generateTokenString();
//
//        $abilities = $this->getAbilities();
//
//        $token = $this->tokens()->create([
//            'name' => $name,
//            'token' => hash('sha256', $plainTextToken),
//            'abilities' => $abilities,
//            'expires_at' => $expiresAt,
//        ]);
//
//        return new NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
//    }


    public function lastActivity()
    {
        return $this->activities()->whereNull('left_at')->first();


    }

    public function left($data = NULL)
    {

        $last_activity = $this->lastActivity();
        if ($last_activity !== NULL) {
            $last_activity->update([
                                       'left_at' => now(),
                                       'data'    => $data,

                                   ]);
        }

    }


    public function getTime($period = NULL)
    {
        $acts = $this->activities();

        if ($period === 'today') {

            $acts = $acts->where('created_at', '>=', today());


        }


        if ($period === 'yesterday') {

            $acts = $acts->where('created_at', '>=', today()->subDay())->where('created_at', '<=', today());


        }

        if ($period === 'currentMonth') {

            $acts = $acts->where('created_at', '>=', now()->firstOfMonth());


        }
        $sum_minutes = 0;
        $data = [];
        $acts = $acts->get();
        foreach ($acts as $act) {


            $left_at = now();

            if ($act->left_at !== NULL) {
                $left_at = $act->left_at;
            }

            $diff = $act->join_at->diffInMinutes($left_at);

            $sum_minutes += $diff;
            $data[] = 'Joined: ' . $act->join_at->timezone('Asia/Tehran')
                                                ->toDateTimeString() . ' Left: ' . $left_at->timezone('Asia/Tehran')
                                                                                           ->toDateTimeString() . ' Diff: ' . $diff;


        }
        \Carbon\CarbonInterval::setCascadeFactors([
                                                      'minute' => [60, 'seconds'],
                                                      'hour'   => [60, 'minutes'],
                                                  ]);

        return [
            'user'        => $this,
            'count'       => $acts->count(),
            'sum_minutes' => $sum_minutes,
            'sum_hours'   => \Carbon\CarbonInterval::minutes($sum_minutes)->cascade()->forHumans(),
            'data'        => $data,
            'activities'  => $acts->map(function ($act) {
                return [
                    'id'         => $act->id,
                    'join_at'    => $act->join_at->timezone('Asia/Tehran')->toDayDateTimeString(),
                    'left_at'    => $act->left_at?->timezone('Asia/Tehran')->toDayDateTimeString(),
                    'created_at' => $act->created_at->timezone('Asia/Tehran')->toDayDateTimeString(),
                ];
            }),
        ];

    }


    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function getAbilities(): array
    {
        $roleIds = $this->roles()->pluck('id');
        $permIds = PermissionRole::whereIn('role_id', $roleIds)->distinct()->pluck('permission_id');
        $perms = Permission::query()->whereIn('id', $permIds)->pluck('name');

        return $perms->toArray();
    }
}
