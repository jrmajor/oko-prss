<?php

namespace Major\OkoPrss;

use Mustache_Engine;
use Psl\File;

/**
 * @param list<Entry> $entries
 */
function generate_atom(array $entries): string
{
    $mustache = new Mustache_Engine();

    $template = File\read(__DIR__ . '/../view/atom.mustache');

    return $mustache->render($template, ['entries' => $entries]);
}
