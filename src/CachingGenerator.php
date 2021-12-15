<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;


use Generator;

class CachingGenerator implements \Iterator
{
    /** @var Generator */
    private $generator;

    /** @var array */
    private $cache = [];

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
        $this->addCurrentToCache();
    }

    public function current()
    {
        return current($this->cache);
    }

    public function next(): void
    {
        if ($this->generator->key() === key($this->cache)) {
            $this->generator->next();
            $this->addCurrentToCache();
        }
        next($this->cache);
    }

    public function key(): ?int
    {
        return key($this->cache);
    }

    public function valid(): bool
    {
        return key($this->cache) !== null;
    }

    public function rewind(): void
    {
        reset($this->cache);
    }

    public function getInnerIterator(): Generator
    {
        return $this->generator;
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    private function addCurrentToCache(): void
    {
        if ($this->generator->valid()) {
            $this->cache[] = $this->generator->current();
        }
    }

    public function getByOffset(int $offset)
    {
        if ($offset === 0) {
            return $this->current();
        }

        if ($offset < 0) {
            return $this->cache[$this->key() - abs($offset)] ?? false;
        }

        $currentKey = $this->key();

        // look ahead
        foreach (range(1, $offset) as $_) {
            if (!$this->valid()) {
                break;
            }
            $this->next();
        }

        $current = $this->current();

        $this->rewind();

        foreach (range(1, $currentKey) as $_) {
            $this->next();
        }

        return $current;
    }
}
