<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;
use Symfony\Component\DomCrawler\Crawler;

const EMAIL_PROTECTION_REGEX = '/<span class="__cf_email__" data-cfemail="[0-9a-z]*">\\[email protected\\]<\\/span>/';

function entry_reducer(Acc $acc, Crawler $el): Acc
{
    // We need to skip junk at the end of the article.
    if ($acc->stopped) {
        return $acc;
    }

    $html = $el->outerHtml();

    // We arrived at the summary, the rest of the article is junk.
    if (Str\starts_with($html, '<h2>') || Str\contains($html, 'ScrollToComment')) {
        return $acc->stop();
    }

    $html = Str\after($html, '<div class="accordion-content" data-tab-content><div class="fc"><p></p>') ?? $html;
    $html = Str\before($html, '<p></p></div><div class="text-center medium-text-right tab-tools">') ?? $html;
    $html = fix_photos($html);
    $html = replace_refs($html);
    $html = Regex\replace($html, EMAIL_PROTECTION_REGEX, '<i>[email protected]</i>');

    // There is no time, this must be a continuation of the current entry.
    if (! $time = match_hour($html, $acc->meta->date)) {
        return $acc->appendCurrent($html);
    }

    // Paragraph starts with an hour, that means this is a new entry.
    return $acc->newEntry($time, Str\contains($html, 'live-blog-update__header') ? '' : $html);
}
