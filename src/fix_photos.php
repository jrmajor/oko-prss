<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;

const WRAPPER_REGEX = '/<p[^>]*>(<img[^>]+\\/?\\s*>)\\s*<\\/p>/';

const PHOTO_REGEX = '/<img.*src="https:\\/\\/[^"]+lazy-archive-white.jpg".*'
    . 'data-srcset="(https:\\/\\/oko.press\\/images\\/[^"\\s]+)/';

const PHOTO_PLACEHOLDER = 'https://oko.press/app/themes/oko/assets/images/lazy-archive-white.jpg';

function fix_photos(string $source): string
{
    $wrapped = Regex\first_match($source, WRAPPER_REGEX);

    $source = $wrapped[1] ?? $source;

    if (
        ! Str\contains($source, 'lazy-archive-white.jpg')
        || null === $photo = Regex\first_match($source, PHOTO_REGEX)
    ) {
        return $source;
    }

    return Str\replace($source, PHOTO_PLACEHOLDER, $photo[1]);
}
