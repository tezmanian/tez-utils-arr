<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

/**
 * Safely read deeply nested values from an array using dot-notation paths.
 *
 * Each segment of the path is resolved in turn. Integer-looking segments
 * (e.g. "0") address list indices. When any segment is missing or has the
 * wrong type, the method returns $default.
 *
 * Example path: 'chart.result.0.meta.currency'
 */
final class SafeGet
{
    /**
     * Read a nested value as a string.
     *
     * @param array<mixed> $data
     */
    public static function string(array $data, string $path, ?string $default = null): ?string
    {
        $value = self::resolve($data, $path);

        if (!is_string($value)) {
            return $default;
        }

        return $value;
    }

    /**
     * Read a nested value as an integer.
     *
     * @param array<mixed> $data
     */
    public static function int(array $data, string $path, ?int $default = null): ?int
    {
        $value = self::resolve($data, $path);

        if (!is_int($value)) {
            return $default;
        }

        return $value;
    }

    /**
     * Read a nested value as a float.
     * Integer values are widened to float automatically.
     *
     * @param array<mixed> $data
     */
    public static function float(array $data, string $path, ?float $default = null): ?float
    {
        $value = self::resolve($data, $path);

        if (is_float($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (float) $value;
        }

        return $default;
    }

    /**
     * Read a nested value as a bool.
     *
     * @param array<mixed> $data
     */
    public static function bool(array $data, string $path, ?bool $default = null): ?bool
    {
        $value = self::resolve($data, $path);

        if (!is_bool($value)) {
            return $default;
        }

        return $value;
    }

    /**
     * Read a nested value as an array.
     *
     * @param array<mixed> $data
     * @param array<mixed>|null $default
     * @return array<mixed>|null
     */
    public static function array(array $data, string $path, ?array $default = null): ?array
    {
        $value = self::resolve($data, $path);

        if (!is_array($value)) {
            return $default;
        }

        return $value;
    }

    /**
     * Walk the dot-notation path and return the raw value, or null when missing.
     *
     * @param array<mixed> $data
     */
    private static function resolve(array $data, string $path): mixed
    {
        $segments = explode('.', $path);
        $current = $data;

        foreach ($segments as $segment) {
            if (!is_array($current)) {
                return null;
            }

            $key = is_numeric($segment) && ctype_digit($segment)
                ? (int) $segment
                : $segment;

            if (!array_key_exists($key, $current)) {
                return null;
            }

            $current = $current[$key];
        }

        return $current;
    }
}
