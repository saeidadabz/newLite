<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'notifiable_type',
        'notifiable_id',
        'message',
        'status',
        'read_at',
        'sends_at',
    ];

    protected $casts = [
        'read_at'  => 'datetime',
        'sends_at' => 'datetime',
    ];

    public function isScheduled(): bool
    {
        return $this->status === NotificationStatus::Scheduled->name;
    }

    public function read(): bool
    {
        return $this->forceFill([
            'read_at' => now(),
        ])->save();
    }
}
