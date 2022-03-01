<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;

const PODCAST_REGEX = '/<div class="podcast-embedded">.*<a href="([^"]+)".*'
    . 'data-src="(https:\\/\\/oko.press\\/images\\/[^"]+)".*<h6[^>]*>([^<]+)<\\/h6>/';

const ARTICLE_REGEX = '/<div class="powiazany-artykul-shortcode">.*<a href="([^"]+)".*'
    . 'data-src="(https:\\/\\/oko.press\\/images\\/[^"]+)".*alt="([^"]+)".*'
    . '<h3[^>]*>([^<]+)<\\/h3>/';

/**
 * Replace references to other OKO.press articles and podcasts.
 */
function replace_refs(string $source): string
{
    if (null !== $podcast = Regex\first_match($source, PODCAST_REGEX)) {
        return Str\format(
            '<p><a href="%s">%s →</a></p> <img src="%s" alt="Powiększenie - podcast OKO.press">',
            $podcast[1], $podcast[3], full_res($podcast[2]),
        );
    }

    if (null !== $article = Regex\first_match($source, ARTICLE_REGEX)) {
        return Str\format(
            '<p><a href="%s">%s →</a></p> <img src="%s" alt="%s">',
            $article[1], $article[4], full_res($article[2]), $article[3],
        );
    }

    return $source;
}
