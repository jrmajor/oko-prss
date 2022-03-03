<?php

namespace Major\OkoPrss;

use Carbon\CarbonImmutable as Carbon;
use Psl\Str;

final class Entry
{
    public function __construct(
        public readonly Meta $meta,
        public readonly Carbon $time,
        public readonly string $source,
    ) { }

    public function id(): string
    {
        return Str\format('%s,%s', $this->meta->url, $this->time->format('H:i'));
    }

    public function title(): string
    {
        $day = $this->time->diffInDays(new Carbon('2022-02-24 04:00'));
        $hour = $this->time->format('G:i');

        return Str\format('Dzień %d, %s', $day + 1, $hour);
    }

    public function timestamp(): string
    {
        return $this->time->toRfc3339String();
    }

    public function append(string $source): self
    {
        return new self(
            $this->meta, $this->time,
            $this->source . ' ' . $source,
        );
    }
}
