<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->append([__DIR__ . '/gen'])
    ->ignoreVCS(true);

return Major\CS\config($finder)
    ->setCacheFile('.cache/.php-cs-fixer.cache');
