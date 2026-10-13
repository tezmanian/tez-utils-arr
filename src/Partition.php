<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class Partition
{
    /**
     * Splits an array into two lists in a single pass.
     * Returns [passing, failing] — items satisfying the predicate come first.
     *
     * @template TValue
     * @param array<TValue> $items
     * @param callable(TValue): bool $predicate
     * @return array{list<TValue>, list<TValue>}
     */
    public static function by(array $items, callable $predicate): array
    {
        $pass = [];
        $fail = [];

        foreach ($items as $item) {
            if ($predicate($item)) {
                $pass[] = $item;
            } else {
                $fail[] = $item;
            }
        }

        return [$pass, $fail];
    }
}
