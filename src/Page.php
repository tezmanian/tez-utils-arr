<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

/**
 * Immutable result of a paginated array slice.
 *
 * @template T
 */
final readonly class Page
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        private array $items,
        private int $total,
        private int $currentPage,
        private int $perPage,
    ) {}

    /** @return list<T> The items on the current page. */
    public function items(): array
    {
        return $this->items;
    }

    /** Returns the total number of items across all pages. */
    public function total(): int
    {
        return $this->total;
    }

    /** Returns the current page number (1-based). */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /** Returns the number of items per page. */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /** Returns the last page number. Always at least 1, even for empty sets. */
    public function lastPage(): int
    {
        return max(1, (int) ceil($this->total / $this->perPage));
    }

    /** Returns true if a next page exists. */
    public function hasNext(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    /** Returns true if a previous page exists (i.e. current page > 1). */
    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }
}
