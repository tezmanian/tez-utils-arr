<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class Find
{
    /**
     * Returns the first item for which $callback returns true, or null if none matches.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<TKey, TValue> $items
     * @param callable(TValue): bool $callback
     * @return TValue|null
     */
    public static function first(array $items, callable $callback): mixed
    {
        foreach ($items as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns the last item for which $callback returns true, or null if none matches.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<TKey, TValue> $items
     * @param callable(TValue): bool $callback
     * @return TValue|null
     */
    public static function last(array $items, callable $callback): mixed
    {
        $match = null;

        foreach ($items as $item) {
            if ($callback($item)) {
                $match = $item;
            }
        }

        return $match;
    }
}
