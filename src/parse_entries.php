<?php

namespace Major\OkoPrss;

use Psl\Iter;
use Psl\Regex;
use Psl\Str;
use Psl\Vec;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @return list<Entry>
 */
function parse_entries(string $source): array
{
    $nodes = (new Crawler($source))
        ->filter('#banner-after-excerpt ~ div.entry-content')
        ->children()
        ->each(fn (Crawler $c) => $c);

    $nodes = Vec\flat_map($nodes, function (Crawler $original): array {
        $isBlock = fn (Crawler $el): bool => Iter\contains([
            'article', 'div', 'section', 'header', 'footer',
        ], $el->nodeName());

        $child = $original;

        while ($isBlock($child) && $child->children()->count() === 1) {
            if (Regex\matches($child->first()->html(), HOUR_REGEX)) {
                return $child->children()->each(fn (Crawler $c) => $c);
            }

            $child = $child->children()->first();
        }

        if (
            $isBlock($child)
            && $child->children()->count() !== 0
            && Regex\matches($child->first()->html(), HOUR_REGEX)
        ) {
            return $child->children()->each(fn (Crawler $c) => $c);
        }

        return $child->nodeName() === 'p' ? [$child] : [$original];
    });

    $nodes = Vec\filter($nodes, function (Crawler $n): bool {
        return ! Str\contains($n->outerHtml(), 'id="intertext-banners"')
            && ! Str\contains($n->html(), '2019/09/europejski-samo-tlo-kwadrat.png');
    });

    $nodes = Vec\filter($nodes, function (Crawler $n): bool {
        return $n->nodeName() !== 'script'
            || ! Str\contains($n->html(), 't="live-news",e="live-blog-update"');
    });

    return Iter\reduce($nodes, entry_reducer(...), new Acc(parse_meta($source)))->entries;
}
