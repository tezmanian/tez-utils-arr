<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\Flatten;

final class FlattenTest extends TestCase
{
    public function testFlattensFullyByDefault(): void
    {
        $result = Flatten::flatten([1, [2, 3], [4, [5, 6]]]);

        self::assertSame([1, 2, 3, 4, 5, 6], $result);
    }

    public function testDepthOne(): void
    {
        $result = Flatten::flatten([1, [2, 3], [4, [5, 6]]], depth: 1);

        self::assertSame([1, 2, 3, 4, [5, 6]], $result);
    }

    public function testDepthTwo(): void
    {
        $result = Flatten::flatten([1, [2, [3, [4]]]], depth: 2);

        self::assertSame([1, 2, 3, [4]], $result);
    }

    public function testAlreadyFlatArrayIsUnchanged(): void
    {
        $result = Flatten::flatten([1, 2, 3]);

        self::assertSame([1, 2, 3], $result);
    }

    public function testEmptyArrayReturnsEmpty(): void
    {
        self::assertSame([], Flatten::flatten([]));
    }

    public function testAssociativeKeysAreDiscarded(): void
    {
        $result = Flatten::flatten([['a' => 1, 'b' => 2], ['c' => 3]]);

        self::assertSame([1, 2, 3], $result);
    }

    public function testDepthZeroReturnsOriginalValues(): void
    {
        $result = Flatten::flatten([[1, 2], [3, 4]], depth: 0);

        self::assertSame([[1, 2], [3, 4]], $result);
    }

    public function testMixedNestingDepths(): void
    {
        $result = Flatten::flatten([1, [2, [3]], 4, [[5]]]);

        self::assertSame([1, 2, 3, 4, 5], $result);
    }
}
