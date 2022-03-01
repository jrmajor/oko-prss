<?php

namespace Major\OkoPrss;

use Psl\Iter;
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

    [$entries] = Iter\reduce(
        $nodes,
        entry_reducer(...),
        [[], Entry::empty(), parse_meta($source)],
    );

    // The first entry is junk, remove it.
    array_shift($entries);

    return $entries;
}
