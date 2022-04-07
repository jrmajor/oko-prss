<?php

namespace Major\OkoPrss;

use Amp;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Request;
use Amp\Http\Client\TimeoutException;
use Amp\Promise;
use Psl\File;
use Psl\Filesystem;
use Psl\IO;

const CACHE_PATH = __DIR__ . '/../.cache/articles';

/**
 * @return Promise<string>
 */
function get_source(HttpClient $client, int $n, string $article): Promise
{
    return Amp\call(function () use ($client, $n, $article) {
        Filesystem\create_directory(CACHE_PATH);

        $cachePath = CACHE_PATH . "/{$n}-{$article}.html";

        $request = new Request("https://oko.press/{$article}");

        $request->setInactivityTimeout(500);
        $request->setTcpConnectTimeout(500);
        $request->setTlsHandshakeTimeout(500);
        $request->setTransferTimeout(500);

        try {
            $response = yield $client->request($request);

            $body = yield $response->getBody()->buffer();

            File\write($cachePath, $body);

            IO\write_line('Fetched day %d.', $n);

            return $body;
        } catch (TimeoutException $e) {
            if (! Filesystem\is_file($cachePath)) {
                throw $e;
            }

            IO\write_line('Failed to fetch day %d, using cache.', $n);

            return File\read($cachePath);
        }
    });
}
