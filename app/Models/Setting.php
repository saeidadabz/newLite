<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key', 'value', 'workspace_id',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
