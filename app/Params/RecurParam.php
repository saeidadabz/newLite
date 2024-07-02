<?php

namespace App\Params;

class RecurParam
{
    public function __construct(
        public readonly int $pattern,
        public readonly string $endDate,
        public readonly array $days = [],
    )
    {
        //
    }
}
