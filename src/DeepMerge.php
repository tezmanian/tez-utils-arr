<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class DeepMerge
{
    /**
     * Recursively merges one or more override arrays into a base array.
     * Overrides are applied left to right.
     *
     * Merge rules:
     * - String key exists only in base  → kept as-is
     * - String key exists only in override → added
     * - Both values are arrays → merged recursively
     * - Override value is scalar or null → overwrites base value
     * - Integer keys → appended (like array_merge)
     *
     * @param array<mixed> $base
     * @param array<mixed> ...$overrides
     * @return array<mixed>
     */
    public static function merge(array $base, array ...$overrides): array
    {
        foreach ($overrides as $override) {
            $base = self::mergeTwo($base, $override);
        }

        return $base;
    }

    /**
     * @param array<mixed> $base
     * @param array<mixed> $override
     * @return array<mixed>
     */
    private static function mergeTwo(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (is_int($key)) {
                // Integer keys are appended, matching array_merge behaviour.
                $base[] = $value;
            } elseif (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
                // Both sides are arrays — recurse.
                $base[$key] = self::mergeTwo($base[$key], $value);
            } else {
                // Scalar, null, or type mismatch — right side wins.
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
