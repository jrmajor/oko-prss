<?php

namespace Major\OkoPrss;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psl\File;
use Psl\Filesystem;
use Psl\Str;

const CACHE_PATH = __DIR__ . '/.cache/articles';

function get_source(string $article): string
{
    Filesystem\create_directory(CACHE_PATH);

    $cachePath = CACHE_PATH . '/' . $article;

    $client = new Client(['base_uri' => 'https://oko.press', 'timeout' => 2.0]);

    try {
        $body = (string) $client->get($article)->getBody();

        File\write($cachePath, $body);

        return $body;
    } catch (GuzzleException $e) {
        if (! Filesystem\is_file($cachePath)) {
            throw $e;
        }

        echo Str\format("Failed to fetch article %s, using cache.\n", $article);

        return File\read($cachePath);
    }
}
