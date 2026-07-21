<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class UniqueBy
{
    /**
     * Returns an array with duplicates removed, where uniqueness is determined by the callback.
     *
     * By default the first occurrence of each key wins and keys are re-indexed.
     * Set $lastWins = true to keep the last occurrence instead.
     * Set $preserveKeys = true to retain the original array keys.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<TKey, TValue> $items
     * @param callable(TValue): (string|int) $callback
     * @return ($preserveKeys is true ? array<TKey, TValue> : list<TValue>)
     */
    public static function filter(
        array $items,
        callable $callback,
        bool $lastWins = false,
        bool $preserveKeys = false,
    ): array {
        if ($lastWins) {
            $items = array_reverse($items, preserve_keys: true);
        }

        $seen   = [];
        $result = [];

        foreach ($items as $key => $item) {
            $uniqueKey = $callback($item);

            if (!isset($seen[$uniqueKey])) {
                $seen[$uniqueKey] = true;
                $result[$key]     = $item;
            }
        }

        if ($lastWins) {
            $result = array_reverse($result, preserve_keys: true);
        }

        return $preserveKeys ? $result : array_values($result);
    }
}
