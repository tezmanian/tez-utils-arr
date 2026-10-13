<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class Sliding
{
    /**
     * Produces consecutive overlapping sub-arrays (windows) of a fixed size.
     * The $step parameter controls how many positions to advance between windows.
     *
     * @param array<mixed> $items
     * @throws \InvalidArgumentException when $size or $step is less than 1
     * @return list<list<mixed>>
     */
    public static function window(array $items, int $size, int $step = 1): array
    {
        if ($size < 1) {
            throw new \InvalidArgumentException('Window size must be >= 1.');
        }

        if ($step < 1) {
            throw new \InvalidArgumentException('Step must be >= 1.');
        }

        $items  = array_values($items);
        $count  = count($items);
        $result = [];

        for ($i = 0; $i + $size <= $count; $i += $step) {
            $result[] = array_slice($items, $i, $size);
        }

        return $result;
    }
}
