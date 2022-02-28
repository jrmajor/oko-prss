<?php

namespace Major\OkoPrss;

use GuzzleHttp\Client;

function get_source(string $article): string
{
    $client = new Client(['base_uri' => 'https://oko.press', 'timeout' => 3.0]);

    return (string) $client->get($article)->getBody();
}
