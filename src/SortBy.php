<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class SortBy
{
    /**
     * Sorts a copy of the array by the value extracted via callback.
     * Does not mutate the original array. Result is always re-indexed.
     *
     * @template TValue
     * @param array<TValue> $items
     * @param callable(TValue): (int|float|string) $keyFn
     * @return list<TValue>
     */
    public static function sort(array $items, callable $keyFn, bool $descending = false): array
    {
        $copy = array_values($items);

        usort($copy, static function ($a, $b) use ($keyFn, $descending): int {
            $ka  = $keyFn($a);
            $kb  = $keyFn($b);
            $cmp = is_string($ka) ? strcmp((string) $ka, (string) $kb) : ($ka <=> $kb);

            return $descending ? -$cmp : $cmp;
        });

        return $copy;
    }
}
