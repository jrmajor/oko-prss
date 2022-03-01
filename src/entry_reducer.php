<?php

namespace Major\OkoPrss;

use Psl\Regex;
use Psl\Str;
use Psl\Type;
use Symfony\Component\DomCrawler\Crawler;

const HOUR_REGEX = '/^<p[^>]*>\\s*<strong[^>]*>\\s*(\\d+:\\d+)[^\d\w]*<.strong>/';

/**
 * Accumulator consists of a list of complete entries and the current entry.
 * If the next element isn't a new entry, it will be appended to the current one.
 *
 * @param array{list<Entry>, ?Entry, Meta} $acc
 * @return array{list<Entry>, ?Entry, Meta}
 */
function entry_reducer(array $acc, Crawler $el): array
{
    [$entries, $current, $meta] = $acc;

    // Current entry being null means we finished parsing entries.
    // We need to skip junk at the end of the article.
    if ($current === null) {
        return [$entries, $current, $meta];
    }

    $html = $el->outerHtml();

    // We arrived at the summary, the rest of the article is junk.
    if (Str\starts_with($html, '<h2>')) {
        return [[...$entries, $current], null, $meta];
    }

    $hour = Regex\first_match($html, HOUR_REGEX)[1] ?? null;

    // Paragraph starts with an hour, that means this is a new entry.
    if ($hour !== null) {
        [$hour, $minute] = Type\vec(Type\int())->coerce(Str\split($hour, ':'));

        // Article may contain entries made after midnight.
        // Let's hope they will sleep at 4 AM.
        $time = $hour < 4 ? $meta->date->addDay() : $meta->date;
        $time = $time->setTime($hour, $minute);

        return [[...$entries, $current], new Entry($meta, $time, $html), $meta];
    }

    // This must be a continuation of the current entry.
    return [$entries, $current->append($html), $meta];
}