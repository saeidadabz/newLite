<?php

namespace App\Utilities;

use Illuminate\Database\Eloquent\Casts\Attribute;

class PersonalAccessToken {
    protected function lastUsedAt(): Attribute {
        return Attribute::make(set: function (mixed $value): void {
            // disable updating the last_used_at attribute as it's not used

            return;
        },);
    }
}
