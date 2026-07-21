<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class Paginator
{
    /**
     * Paginates an array and returns an immutable Page with slice and metadata.
     *
     * @template T
     * @param list<T> $items
     *
     * @throws \InvalidArgumentException if $page < 1 or $perPage < 1
     * @return Page<T>
     */
    public static function paginate(array $items, int $page, int $perPage): Page
    {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page must be >= 1, got ' . $page . '.');
        }

        if ($perPage < 1) {
            throw new \InvalidArgumentException('Per-page must be >= 1, got ' . $perPage . '.');
        }

        $total  = count($items);
        $offset = ($page - 1) * $perPage;
        $slice  = array_values(array_slice($items, $offset, $perPage));

        return new Page($slice, $total, $page, $perPage);
    }
}
