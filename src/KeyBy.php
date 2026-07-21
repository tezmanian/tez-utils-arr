<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class KeyBy
{
    /**
     * Builds an associative lookup map by indexing each item under the key
     * returned from the callback. If two items produce the same key, the last wins.
     *
     * @template TKey of int|string
     * @template TValue
     * @param array<TValue> $items
     * @param callable(TValue): TKey $keyFn
     * @return array<TKey, TValue>
     */
    public static function index(array $items, callable $keyFn): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[$keyFn($item)] = $item;
        }

        return $result;
    }
}
