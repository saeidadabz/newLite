<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'availability_type',
        'user_id',
        'days',
        'starts_at',
        'ends_at',
    ];

//    protected $casts = [
//        'starts_at' => 'datetime:' . Constants::SCHEDULE_DATE_FORMAT,
//        'ends_at' => 'datetime:' . Constants::SCHEDULE_DATE_FORMAT,
//    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    public function loadExpands(string $relations)
//    {
//        $relations = explode(',', $relations);
//        array_map(fn($r) => in_array($r, ['owner', 'calendar']) && $this->load($r), $relations);
//
//        return $this;
//    }
}
