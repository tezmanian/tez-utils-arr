<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\Zip;

final class ZipTest extends TestCase
{
    public function testZipEqualLengthArrays(): void
    {
        $result = Zip::zip([1, 2, 3], ['a', 'b', 'c']);

        self::assertSame([[1, 'a'], [2, 'b'], [3, 'c']], $result);
    }

    public function testZipPadsShorterArrayWithNull(): void
    {
        $result = Zip::zip([1, 2, 3], ['a', 'b']);

        self::assertSame([[1, 'a'], [2, 'b'], [3, null]], $result);
    }

    public function testZipThreeArrays(): void
    {
        $result = Zip::zip([1, 2], ['a', 'b'], [true, false]);

        self::assertSame([[1, 'a', true], [2, 'b', false]], $result);
    }

    public function testZipNoArraysReturnsEmpty(): void
    {
        self::assertSame([], Zip::zip());
    }

    public function testZipEmptyArraysReturnEmpty(): void
    {
        self::assertSame([], Zip::zip([], []));
    }

    public function testShortestStopsAtSmallest(): void
    {
        $result = Zip::shortest([1, 2, 3], ['a', 'b']);

        self::assertSame([[1, 'a'], [2, 'b']], $result);
    }

    public function testShortestWithEqualLengths(): void
    {
        $result = Zip::shortest([1, 2], ['a', 'b']);

        self::assertSame([[1, 'a'], [2, 'b']], $result);
    }

    public function testShortestWithEmptyArrayReturnsEmpty(): void
    {
        self::assertSame([], Zip::shortest([1, 2, 3], []));
    }

    public function testShortestNoArraysReturnsEmpty(): void
    {
        self::assertSame([], Zip::shortest());
    }

    public function testZipStripsAssociativeKeys(): void
    {
        $result = Zip::zip(['a' => 1, 'b' => 2], ['x' => 10, 'y' => 20]);

        self::assertSame([[1, 10], [2, 20]], $result);
    }
}
