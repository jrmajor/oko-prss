<?php

namespace Major\OkoPrss;

use Carbon\CarbonImmutable as Carbon;
use Psl\Regex;
use Psl\Type;

const HourRegExOld = '<(?:p|div)[^>]*>\\s*<(?:strong|b)[^>]*>\\s*(\\d+)\\s*[:.]\\s*(\\d+)[^\\d\\w]';
const HourRegExNew = '<div class="live-blog-update.*<time.*datetime="([^"]*)">.*<\\/time><\\/div>';

const HourRegEx = '/^(?:(?:' . HourRegExOld . ')|(?:' . HourRegExNew . '))/';

function match_hour(string $html, Carbon $date): ?Carbon
{
    if (! $match = Regex\first_match($html, HourRegEx)) {
        return null;
    }

    if ($match[3] ?? false) {
        return new Carbon($match[3]);
    }

    $hour = Type\int()->coerce($match[1]);
    $minute = Type\int()->coerce($match[2]);

    // Article may contain entries made after midnight.
    // Let's hope they will sleep at 4 AM.
    $day = $hour < 4 ? $date->addDay() : $date;

    return $day->setTime($hour, $minute);
}
