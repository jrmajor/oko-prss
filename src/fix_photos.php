<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;

const WrapperRegEx = '/<p[^>]*>(<img[^>]+\\/?\\s*>)\\s*<\\/p>/';

const PhotoRegEx = '/<img[\\S\\s]*src="https:\\/\\/[^"]+lazy-archive-white.jpg"[\\S\\s]*'
    . 'data-srcset="(https:\\/\\/oko\\.press\\/images\\/[^"\\s]+)/';

const PhotoPlaceholder = 'https://oko.press/app/themes/oko/assets/images/lazy-archive-white.jpg';

function fix_photos(string $source): string
{
    $wrapped = Regex\first_match($source, WrapperRegEx);

    $source = $wrapped[1] ?? $source;

    if (
        ! Str\contains($source, 'lazy-archive-white.jpg')
        || null === $photo = Regex\first_match($source, PhotoRegEx)
    ) {
        return $source;
    }

    return Str\replace($source, PhotoPlaceholder, $photo[1]);
}
