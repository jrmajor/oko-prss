<?php

namespace Major\OkoPrss;

use Psl\Html;
use Psl\Regex;
use Psl\Str;

const PODCAST_REGEX = '/<div class="podcast-embedded">[\\S\\s]*<a href="([^"]+)"[\\S\\s]*'
    . 'data-src="(https:\\/\\/oko\\.press\\/images\\/[^"]+)"[\\S\\s]*<h6[^>]*>([^<]+)<\\/h6>/';

const ARTICLE_REGEX = '/<div class="powiazany-artykul-shortcode">[\\S\\s]*<a href="([^"]+)"[\\S\\s]*'
    . 'data-src="(https:\\/\\/oko\\.press\\/images\\/[^"]+)"[\\S\\s]*alt=("[^"]+"|\'[^\']+\')[\\S\\s]*'
    . '<h3[^>]*>([^<]+)<\\/h3>/';

const MAP_REGEX = '/flourish-map.*data-(?:url|src)="([^"]+flourish\\.studio\\/[^"]+\\/embed)(?:\\?[^"]*)?"/';

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
        /** @psalm-suppress InvalidArgument Str\slice works with negative length. */
        $alt = Html\encode(Str\slice($article[3], 1, -1));

        return Str\format(
            '<p><a href="%s">%s →</a></p> <img src="%s" alt="%s">',
            $article[1], $article[4], full_res($article[2]), $alt,
        );
    }

    if (null !== $map = Regex\first_match($source, MAP_REGEX)) {
        return Str\format('<p><a href="%s">Zobacz na mapie →</a></p>', $map[1]);
    }

    return $source;
}
