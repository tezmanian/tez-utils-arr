<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\Sliding;

final class SlidingTest extends TestCase
{
    public function testDefaultStepOfOne(): void
    {
        $result = Sliding::window([1, 2, 3, 4, 5], size: 3);

        self::assertSame([[1, 2, 3], [2, 3, 4], [3, 4, 5]], $result);
    }

    public function testCustomStep(): void
    {
        $result = Sliding::window([1, 2, 3, 4, 5], size: 3, step: 2);

        self::assertSame([[1, 2, 3], [3, 4, 5]], $result);
    }

    public function testStepEqualsSizeProducesNonOverlappingChunks(): void
    {
        $result = Sliding::window([1, 2, 3, 4, 5, 6], size: 2, step: 2);

        self::assertSame([[1, 2], [3, 4], [5, 6]], $result);
    }

    public function testArraySmallerThanWindowReturnsEmpty(): void
    {
        self::assertSame([], Sliding::window([1, 2], size: 5));
    }

    public function testExactFitProducesOneWindow(): void
    {
        $result = Sliding::window([1, 2, 3], size: 3);

        self::assertSame([[1, 2, 3]], $result);
    }

    public function testSizeOneReturnsEachElementWrapped(): void
    {
        $result = Sliding::window([1, 2, 3], size: 1);

        self::assertSame([[1], [2], [3]], $result);
    }

    public function testEmptyArrayReturnsEmpty(): void
    {
        self::assertSame([], Sliding::window([], size: 3));
    }

    public function testInvalidSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Sliding::window([1, 2, 3], size: 0);
    }

    public function testInvalidStepThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Sliding::window([1, 2, 3], size: 2, step: 0);
    }

    public function testAssociativeKeysAreStripped(): void
    {
        $result = Sliding::window(['a' => 1, 'b' => 2, 'c' => 3], size: 2);

        self::assertSame([[1, 2], [2, 3]], $result);
    }
}
