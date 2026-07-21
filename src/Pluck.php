<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

final class Pluck
{
    /**
     * Extracts the values of a single dot-notation key from an array of records.
     * Items where the key is absent are skipped.
     *
     * @param array<array<mixed>> $items
     * @return list<mixed>
     */
    public static function values(array $items, string $key): array
    {
        $result = [];

        foreach ($items as $item) {
            [$found, $value] = self::resolve($item, $key);

            if ($found) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns an associative array indexed by $indexKey, with $valueKey as the value.
     * Items where either key is absent are skipped.
     *
     * @param array<array<mixed>> $items
     * @return array<int|string, mixed>
     */
    public static function keyedBy(array $items, string $valueKey, string $indexKey): array
    {
        $result = [];

        foreach ($items as $item) {
            [$foundIdx, $idx]   = self::resolve($item, $indexKey);
            [$foundVal, $value] = self::resolve($item, $valueKey);

            if ($foundIdx && $foundVal && (is_int($idx) || is_string($idx))) {
                $result[$idx] = $value;
            }
        }

        return $result;
    }

    /**
     * Resolves a dot-notation key against an array.
     * Returns [true, $value] on success, [false, null] when any segment is missing.
     *
     * @param array<mixed> $data
     * @return array{bool, mixed}
     */
    private static function resolve(array $data, string $key): array
    {
        $parts   = explode('.', $key);
        $current = $data;

        foreach ($parts as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return [false, null];
            }

            $current = $current[$part];
        }

        return [true, $current];
    }
}
