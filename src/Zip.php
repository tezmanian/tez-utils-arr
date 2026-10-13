<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class Zip
{
    /**
     * Combines multiple arrays element-by-element into a list of tuples.
     * Shorter arrays are padded with null to match the longest input.
     *
     * @param array<mixed> ...$arrays
     * @return list<list<mixed>>
     */
    public static function zip(array ...$arrays): array
    {
        if ($arrays === []) {
            return [];
        }

        $arrays = array_map('array_values', $arrays);
        $maxLen = (int) max(array_map('count', $arrays));
        $result = [];

        for ($i = 0; $i < $maxLen; $i++) {
            $tuple = [];

            foreach ($arrays as $arr) {
                $tuple[] = $arr[$i] ?? null;
            }

            $result[] = $tuple;
        }

        return $result;
    }

    /**
     * Combines multiple arrays element-by-element, stopping at the shortest input.
     *
     * @param array<mixed> ...$arrays
     * @return list<list<mixed>>
     */
    public static function shortest(array ...$arrays): array
    {
        if ($arrays === []) {
            return [];
        }

        $arrays = array_map('array_values', $arrays);
        $minLen = (int) min(array_map('count', $arrays));
        $result = [];

        for ($i = 0; $i < $minLen; $i++) {
            $tuple = [];

            foreach ($arrays as $arr) {
                $tuple[] = $arr[$i];
            }

            $result[] = $tuple;
        }

        return $result;
    }
}
