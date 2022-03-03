<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;
use Psl\Type;
use Symfony\Component\DomCrawler\Crawler;

const HOUR_REGEX = '/^<(?:p|div)[^>]*>\\s*<strong[^>]*>\\s*(\\d+)\\s*[:.]\\s*(\\d+)[^\\d\\w]/';

function entry_reducer(Acc $acc, Crawler $el): Acc
{
    // Current entry being null means we finished parsing entries.
    // We need to skip junk at the end of the article.
    if ($acc->stopped) {
        return $acc;
    }

    $html = $el->outerHtml();

    // We arrived at the summary, the rest of the article is junk.
    if (Str\starts_with($html, '<h2>') || Str\contains($html, 'ScrollToComment')) {
        return $acc->stop();
    }

    $hourMatch = Regex\first_match($html, HOUR_REGEX);

    $html = fix_photos($html);
    $html = replace_refs($html);

    // Paragraph starts with an hour, that means this is a new entry.
    if ($hourMatch !== null) {
        $hour = Type\int()->coerce($hourMatch[1]);
        $minute = Type\int()->coerce($hourMatch[2]);

        // Article may contain entries made after midnight.
        // Let's hope they will sleep at 4 AM.
        $time = $hour < 4 ? $acc->meta->date->addDay() : $acc->meta->date;
        $time = $time->setTime($hour, $minute);

        return $acc->newEntry($time, $html);
    }

    // This must be a continuation of the current entry.
    return $acc->appendCurrent($html);
}
