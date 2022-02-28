<?php

namespace Major\OkoPrss;

use Carbon\CarbonImmutable as Carbon;

final class Meta
{
    public function __construct(
        public readonly string $author,
        public readonly Carbon $date,
        public readonly string $url,
    ) { }
}
