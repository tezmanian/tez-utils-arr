<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\CartesianProduct;

final class CartesianProductTest extends TestCase
{
    public function testEmptySetsYieldsOneEmptyTuple(): void
    {
        $result = iterator_to_array(CartesianProduct::create(), false);

        self::assertSame([[]], $result);
    }

    public function testSingleSet(): void
    {
        $result = iterator_to_array(CartesianProduct::create([1, 2, 3]), false);

        self::assertSame([[1], [2], [3]], $result);
    }

    public function testTwoSets(): void
    {
        $result = iterator_to_array(CartesianProduct::create([1, 2], ['a', 'b']), false);

        self::assertSame(
            [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            $result,
        );
    }

    public function testThreeSets(): void
    {
        $result = iterator_to_array(
            CartesianProduct::create(['x', 'y'], [1, 2], ['a', 'b']),
            false,
        );

        self::assertCount(8, $result);
        self::assertSame(['x', 1, 'a'], $result[0]);
        self::assertSame(['x', 1, 'b'], $result[1]);
        self::assertSame(['x', 2, 'a'], $result[2]);
        self::assertSame(['x', 2, 'b'], $result[3]);
        self::assertSame(['y', 1, 'a'], $result[4]);
        self::assertSame(['y', 2, 'b'], $result[7]);
    }

    public function testAssociativeArrayKeysAreStripped(): void
    {
        $result = iterator_to_array(
            CartesianProduct::create(['foo' => 'A', 'bar' => 'B'], ['x' => 1, 'y' => 2]),
            false,
        );

        self::assertSame(
            [['A', 1], ['A', 2], ['B', 1], ['B', 2]],
            $result,
        );
    }

    public function testTotalCountEqualsProductOfSetSizes(): void
    {
        $result = iterator_to_array(CartesianProduct::create([1, 2, 3], ['a', 'b'], [true, false]), false);

        self::assertCount(3 * 2 * 2, $result);
    }

    public function testSingleElementSets(): void
    {
        $result = iterator_to_array(CartesianProduct::create(['only'], [42]), false);

        self::assertSame([['only', 42]], $result);
    }

    public function testGeneratorAsInput(): void
    {
        $gen = (static function (): \Generator {
            yield 10;
            yield 20;
        })();

        $result = iterator_to_array(CartesianProduct::create([1, 2], $gen), false);

        self::assertSame([[1, 10], [1, 20], [2, 10], [2, 20]], $result);
    }

    public function testReturnsGenerator(): void
    {
        $generator = CartesianProduct::create([1], [2]);

        self::assertInstanceOf(\Generator::class, $generator);
    }
}
