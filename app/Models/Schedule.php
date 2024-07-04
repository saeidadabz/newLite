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
        'calendar_id',
        'owner_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'calendar_id'       => 'int',
        'owner_id'          => 'int',
        'availability_type' => 'int',
        'starts_at'         => 'datetime:'.Constants::SCHEDULE_DATE_FORMAT,
        'ends_at'           => 'datetime:'.Constants::SCHEDULE_DATE_FORMAT,
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    public function loadExpands(string $relations)
    {
        $relations = explode(',', $relations);
        array_map(fn ($r) => in_array($r, ['owner', 'calendar']) && $this->load($r), $relations);

        return $this;
    }
}
