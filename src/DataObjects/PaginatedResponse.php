<?php

namespace Nashra\Sdk\DataObjects;

use Closure;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final class PaginatedResponse implements IteratorAggregate
{
    /**
     * @param  T[]  $items
     * @param  Closure(int): PaginatedResponse<T>  $fetcher
     */
    public function __construct(
        private array $items,
        private int $currentPage,
        private int $lastPage,
        private int $total,
        private int $perPage,
        private Closure $fetcher,
    ) {}

    /**
     * @return T[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        foreach ($this->items as $item) {
            yield $item;
        }

        $page = $this->currentPage;
        while ($page < $this->lastPage) {
            $page++;
            $next = ($this->fetcher)($page);
            foreach ($next->items() as $item) {
                yield $item;
            }
        }
    }
}
