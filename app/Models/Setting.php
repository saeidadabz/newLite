<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'settingable_type',
        'settingable_id',
    ];

    public function settingable()
    {
        return $this->morphTo();
    }
}
