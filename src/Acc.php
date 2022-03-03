<?php

namespace Major\OkoPrss;

use Carbon\CarbonImmutable as Carbon;

final class Acc
{
    public function __construct(
        public readonly Meta $meta,
        /** @var list<Entry> */
        public readonly array $entries = [],
        public readonly ?Entry $current = null,
        public readonly bool $stopped = false,
    ) { }

    public function appendCurrent(string $source): self
    {
        $entry = $this->current?->append($source);

        return new self($this->meta, $this->entries, $entry, $this->stopped);
    }

    public function newEntry(Carbon $time, string $source): self
    {
        $new = new Entry($this->meta, $time, $source);

        return new self($this->meta, $this->mergeCurrent(), $new, $this->stopped);
    }

    public function stop(): self
    {
        return new self($this->meta, $this->mergeCurrent(), null, true);
    }

    /**
     * @return list<Entry>
     */
    private function mergeCurrent(): array
    {
        if ($this->current === null) {
            return $this->entries;
        }

        return [...$this->entries, $this->current];
    }
}
