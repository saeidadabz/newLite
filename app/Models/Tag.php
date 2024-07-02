<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $fillable = [
        'title',
        'workspace_id'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
