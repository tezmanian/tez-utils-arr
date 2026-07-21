<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\Transpose;

final class TransposeTest extends TestCase
{
    public function testTransposesMatrix(): void
    {
        $result = Transpose::matrix([
            [1, 2, 3],
            [4, 5, 6],
        ]);

        self::assertSame([[1, 4], [2, 5], [3, 6]], $result);
    }

    public function testTransposesSquareMatrix(): void
    {
        $result = Transpose::matrix([
            [1, 2],
            [3, 4],
        ]);

        self::assertSame([[1, 3], [2, 4]], $result);
    }

    public function testTransposesSingleRow(): void
    {
        $result = Transpose::matrix([[1, 2, 3]]);

        self::assertSame([[1], [2], [3]], $result);
    }

    public function testTransposesSingleColumn(): void
    {
        $result = Transpose::matrix([[1], [2], [3]]);

        self::assertSame([[1, 2, 3]], $result);
    }

    public function testEmptyInputReturnsEmpty(): void
    {
        self::assertSame([], Transpose::matrix([]));
    }

    public function testAssociativeKeysAreStripped(): void
    {
        $result = Transpose::matrix([
            ['a' => 1, 'b' => 2],
            ['a' => 3, 'b' => 4],
        ]);

        self::assertSame([[1, 3], [2, 4]], $result);
    }

    public function testJaggedArrayThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Transpose::matrix([[1, 2, 3], [4, 5]]);
    }
}
