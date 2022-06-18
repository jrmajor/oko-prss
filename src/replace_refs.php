<?php

namespace Major\OkoPrss;

use Psl\Html;
use Psl\Regex;
use Psl\Str;

const PodcastRegEx = '/<div class="podcast-embedded">[\\S\\s]*<a href="([^"]+)"[\\S\\s]*'
    . 'data-src="(https:\\/\\/oko\\.press\\/images\\/[^"]+)"[\\S\\s]*<h6[^>]*>([^<]+)<\\/h6>/';

const ArticleRegEx = '/<div class="powiazany-artykul-shortcode">[\\S\\s]*<a href="([^"]+)"[\\S\\s]*'
    . 'data-src="(https:\\/\\/oko\\.press\\/images\\/[^"]+)"[\\S\\s]*alt=("[^"]+"|\'[^\']+\')[\\S\\s]*'
    . '<h3[^>]*>([^<]+)<\\/h3>/';

const MapRegEx = '/flourish-map.*data-(?:url|src)="([^"]+flourish\\.studio\\/[^"]+\\/embed)(?:\\?[^"]*)?"/';

const ChartRegEx = '/flourish-chart.*data-info="([^"]+) \\(\\d+\\)".*data-(?:url|src)="([^"]+flourish\\.studio\\/[^"]+\\/embed)(?:\\?[^"]*)?"/';

/**
 * Replace references to other OKO.press articles and podcasts.
 */
function replace_refs(string $source): string
{
    if (null !== $podcast = Regex\first_match($source, PodcastRegEx)) {
        return Str\format(
            '<p><a href="%s">%s →</a></p> <img src="%s" alt="Powiększenie - podcast OKO.press">',
            $podcast[1], $podcast[3], full_res($podcast[2]),
        );
    }

    if (null !== $article = Regex\first_match($source, ArticleRegEx)) {
        /** @psalm-suppress InvalidArgument Str\slice works with negative length. */
        $alt = Html\encode(Str\slice($article[3], 1, -1));

        return Str\format(
            '<p><a href="%s">%s →</a></p> <img src="%s" alt="%s">',
            $article[1], $article[4], full_res($article[2]), $alt,
        );
    }

    if (null !== $map = Regex\first_match($source, MapRegEx)) {
        return Str\format('<p><a href="%s">Zobacz na mapie →</a></p>', $map[1]);
    }

    if (null !== $map = Regex\first_match($source, ChartRegEx)) {
        return Str\format('<p><a href="%s">%s →</a></p>', $map[2], $map[1]);
    }

    return $source;
}
