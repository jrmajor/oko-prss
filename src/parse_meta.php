<?php

namespace Major\OkoPrss;

use Carbon\CarbonImmutable as Carbon;
use Psl\Json;
use Psl\Str;
use Psl\Type;
use Symfony\Component\DomCrawler\Crawler;

function parse_meta(string $source): Meta
{
    $json = (new Crawler($source))
        ->filter('script[type="application/ld+json"]')
        ->first()
        ->html();

    $data = Type\shape([
        'author' => Type\shape(['name' => Type\string()]),
        'datePublished' => Type\string(),
        'mainEntityOfPage' => Type\shape(['@id' => Type\string()]),
    ])->coerce(Json\decode($json));

    return new Meta(
        $data['author']['name'],
        (new Carbon($data['datePublished']))->setTime(0, 0),
        Str\trim($data['mainEntityOfPage']['@id']),
    );
}
