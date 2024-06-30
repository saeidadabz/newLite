<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'path',
        'type',
        'fileable_id',
        'fileable_type',
        'mime_type'
    ];

    protected $appends = ['url'];


    public static function syncFile($file_id, $model, $type = NULL)
    {
        if ($file_id !== NULL) {
            $file = self::find($file_id);
            if ($file !== NULL) {
                $file->update([
                                  'fileable_type' => get_class($model),
                                  'fileable_id'   => $model->id,
                                  'type'          => $type,
                              ]);
            }

        }

    }

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);

    }

    public function fileable()
    {
        return $this->morphTo();
    }
}
