<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class Wrap
{
    /**
     * Ensures the given value is wrapped in an array.
     * null becomes [], an existing array is returned as-is,
     * any other value is wrapped in a single-element array.
     *
     * @return array<mixed>
     */
    public static function ensure(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }
}
