<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

/**
 * Computes the Cartesian product of any number of sets lazily via a Generator.
 *
 * Each yielded value is a tuple (list) containing one element from every input set.
 * The number of tuples equals the product of all set sizes.
 * Associative keys in input sets are stripped; only values are used.
 */
class CartesianProduct
{
    /**
     * Yields every combination of one element from each provided set.
     * The result is computed lazily — no intermediate array is built.
     * Passing no sets yields a single empty tuple.
     *
     * @param iterable<mixed> ...$sets
     */
    public static function create(iterable ...$sets): \Generator
    {
        if (empty($sets)) {
            yield [];

            return;
        }

        $sets   = array_map(fn($set) => self::toIndexedArray($set), $sets);
        $counts = array_map('count', $sets);
        $total  = array_product($counts);

        for ($i = 0; $i < $total; $i++) {
            $tuple   = [];
            $divisor = 1;

            // Mixed-radix index decomposition: walk sets right-to-left so that the
            // rightmost set cycles fastest, matching standard Cartesian-product order.
            for ($j = count($sets) - 1; $j >= 0; $j--) {
                $index   = (int) ($i / $divisor) % $counts[$j];
                $tuple[] = $sets[$j][$index];
                $divisor *= $counts[$j];
            }

            yield array_reverse($tuple);
        }
    }

    /**
     * @param iterable<mixed> $set
     * @return list<mixed>
     */
    private static function toIndexedArray(iterable $set): array
    {
        return match (true) {
            is_array($set)     => array_values($set),
            default            => iterator_to_array($set, false),
        };
    }

}
