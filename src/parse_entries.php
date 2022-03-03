<?php

namespace Major\OkoPrss;

use Psl\Iter;
use Psl\Regex;
use Psl\Str;
use Psl\Vec;
use Symfony\Component\DomCrawler\Crawler;

const MODULE_START = '<article class="entry"><div class="content">'
    . '<section class="body"><div class="module"><div class="module">';

/**
 * @return list<Entry>
 */
function parse_entries(string $source): array
{
    $nodes = (new Crawler($source))
        ->filter('#banner-after-excerpt ~ div.entry-content')
        ->children()
        ->each(fn (Crawler $c) => $c);

    $nodes = Vec\flat_map($nodes, function (Crawler $c): array {
        if ($c->nodeName() === 'div' && Regex\matches($c->html(), HOUR_REGEX)) {
            return $c->children()->each(fn (Crawler $c) => $c);
        }

        if (
            $c->nodeName() === 'article'
            && Str\starts_with($c->outerHtml(), MODULE_START)
        ) {
            return $c->first()->first()->first()->first()->each(fn (Crawler $c) => $c);
        }

        return [$c];
    });

    $nodes = Vec\filter($nodes, function (Crawler $n): bool {
        return ! Str\contains($n->html(), 'bannersData = bannersData[roulette];')
            && ! Str\contains($n->html(), '2019/09/europejski-samo-tlo-kwadrat.png');
    });

    return Iter\reduce($nodes, entry_reducer(...), new Acc(parse_meta($source)))->entries;
}
