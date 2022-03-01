<?php

namespace Major\OkoPrss;

use Psl\Regex;

/**
 * Remove suffix like "-440x280.jpg" from photo url.
 */
function full_res(string $url): string
{
    return Regex\replace_with($url, '/-\\d+x\\d+(\\.\\w+)$/', fn (array $m) => $m[1]);
}
