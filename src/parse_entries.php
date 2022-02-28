<?php

namespace Major\OkoPrss;

use Psl\Iter;
use Psl\Str;
use Psl\Vec;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @return list<Entry>
 */
function parse_entries(string $source): array
{
    $nodes = (new Crawler($source))
        ->filter('#banner-after-excerpt + div')
        ->children()
        ->each(fn (Crawler $c) => $c);

    // Filter out references to other articles.
    $nodes = Vec\filter($nodes, function (Crawler $el) {
        return $el->nodeName() !== 'div'
            || ! Str\contains($el->attr('class') ?? '', 'powiazany-artykul-shortcode');
    });

    [$entries] = Iter\reduce(
        $nodes,
        entry_reducer(...),
        [[], Entry::empty(), parse_meta($source)],
    );

    // The first entry is junk, remove it.
    array_shift($entries);

    return $entries;
}
