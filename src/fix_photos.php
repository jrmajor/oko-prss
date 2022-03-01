<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;

const PHOTO_REGEX = '/data-srcset="(https:\\/\\/oko.press\\/images\\/[^"\\s]+)/';
const PHOTO_PLACEHOLDER = 'src="https://oko.press/app/themes/oko/assets/images/lazy-archive-white.jpg"';

function fix_photos(string $source): string
{
    if (
        ! Str\starts_with($source, '<figure')
        || null === $url = Regex\first_match($source, PHOTO_REGEX)
    ) {
        return $source;
    }

    return Str\replace($source, PHOTO_PLACEHOLDER, Str\format('src="%s"', $url[1]));
}
