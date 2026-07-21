<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class MapWithKeys
{
    /**
     * Maps over an array where the callback returns a new [key => value] pair.
     * If two items produce the same key, the last one wins.
     *
     * @param array<mixed> $items
     * @param callable(mixed, int|string): array<int|string, mixed> $fn
     * @return array<int|string, mixed>
     */
    public static function map(array $items, callable $fn): array
    {
        $result = [];

        foreach ($items as $key => $value) {
            foreach ($fn($value, $key) as $newKey => $newValue) {
                $result[$newKey] = $newValue;
            }
        }

        return $result;
    }
}
