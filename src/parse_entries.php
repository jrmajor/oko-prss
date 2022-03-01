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
        ->filter('#banner-after-excerpt ~ div.entry-content')
        ->children()
        ->each(fn (Crawler $c) => $c);

    $nodes = Vec\filter($nodes, function (Crawler $n): bool {
        return ! Str\contains($n->html(), 'bannersData = bannersData[roulette];');
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
