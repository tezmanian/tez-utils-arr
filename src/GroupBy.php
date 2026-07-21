<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class GroupBy
{
    /**
     * Groups the items of an array by the key returned from a callback.
     *
     * @template TKey of int|string
     * @template TValue
     * @param array<TValue> $items
     * @param callable(TValue): TKey $keyFn
     * @return array<TKey, list<TValue>>
     */
    public static function group(array $items, callable $keyFn): array
    {
        $result = [];

        foreach ($items as $item) {
            $key            = $keyFn($item);
            $result[$key][] = $item;
        }

        return $result;
    }
}
