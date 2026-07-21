<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class Flatten
{
    /**
     * Recursively flattens a nested array into a single flat list.
     * Associative keys in nested arrays are discarded.
     *
     * @param array<mixed> $array
     * @return list<mixed>
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];
        self::flattenInto($array, $depth, $result);

        return $result;
    }

    /**
     * @param array<mixed> $array
     * @param list<mixed> $result
     */
    private static function flattenInto(array $array, int $depth, array &$result): void
    {
        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                self::flattenInto($item, $depth - 1, $result);
            } else {
                $result[] = $item;
            }
        }
    }
}
