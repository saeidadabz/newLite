<?php

namespace App\Utilities;

use App\Models\Setting;

trait Settingable
{


    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }
}
