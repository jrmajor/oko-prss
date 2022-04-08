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

        $request->setInactivityTimeout(2000);
        $request->setTcpConnectTimeout(2000);
        $request->setTlsHandshakeTimeout(2000);
        $request->setTransferTimeout(2000);

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

            IO\write_line("\e[33mFailed to fetch day %d, using cache.\e[0m", $n);

            return File\read($cachePath);
        }
    });
}
