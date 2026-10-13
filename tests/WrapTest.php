<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\Wrap;

final class WrapTest extends TestCase
{
    public function testWrapsScalarInArray(): void
    {
        self::assertSame(['hello'], Wrap::ensure('hello'));
        self::assertSame([42], Wrap::ensure(42));
        self::assertSame([3.14], Wrap::ensure(3.14));
        self::assertSame([true], Wrap::ensure(true));
    }

    public function testReturnsArrayAsIs(): void
    {
        self::assertSame([1, 2, 3], Wrap::ensure([1, 2, 3]));
        self::assertSame(['a' => 1], Wrap::ensure(['a' => 1]));
        self::assertSame([], Wrap::ensure([]));
    }

    public function testNullReturnsEmptyArray(): void
    {
        self::assertSame([], Wrap::ensure(null));
    }

    public function testObjectIsWrapped(): void
    {
        $obj    = new \stdClass();
        $result = Wrap::ensure($obj);

        self::assertCount(1, $result);
        self::assertSame($obj, $result[0]);
    }

    public function testAssociativeArrayIsReturnedAsIs(): void
    {
        $input  = ['key' => 'value', 'other' => 42];
        $result = Wrap::ensure($input);

        self::assertSame($input, $result);
    }
}
