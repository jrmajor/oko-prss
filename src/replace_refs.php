<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;

const PODCAST_REGEX = '/<div class="podcast-embedded">.*<a href="([^"]+)".*'
    . 'data-src="(https:\\/\\/oko.press\\/images\\/[^"]+)".*<h6[^>]*>([^<]+)<\\/h6>/';

/**
 * Replace references to other OKO.press articles and podcasts.
 */
function replace_refs(string $source): string
{
    $podcast = Regex\first_match($source, PODCAST_REGEX);

    if ($podcast !== null) {
        return Str\format(
            '<p><a href="%s">%s →</a></p> <img src="%s" alt="Powiększenie - podcast OKO.press">',
            $podcast[1], $podcast[3], full_res($podcast[2]),
        );
    }

    return $source;
}
