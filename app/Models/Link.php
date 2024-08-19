<?php

namespace App\Models;

use App\Utilities\Codeable;
use Illuminate\Database\Eloquent\Model;

class Link extends Model {

    use Codeable;


    protected $fillable = ['url', 'text', 'message_id', 'start_position'];


    public function message() {
        return $this->belongsTo(Message::class);
    }

}
