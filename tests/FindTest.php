<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\Find;

final class FindTest extends TestCase
{
    public function testFirstReturnsFirstMatch(): void
    {
        $items = [1, 2, 3, 4, 5];

        $result = Find::first($items, fn($v) => $v > 2);

        self::assertSame(3, $result);
    }

    public function testFirstReturnsNullWhenNoMatch(): void
    {
        /** @var list<int> $items */
        $items  = [1, 2, 3];
        $result = Find::first($items, fn($v) => $v > 10);

        self::assertNull($result);
    }

    public function testFirstReturnsNullOnEmptyArray(): void
    {
        $result = Find::first([], fn($v) => true);

        self::assertNull($result);
    }

    public function testFirstStopsAtFirstMatch(): void
    {
        $calls = 0;
        $items = [1, 2, 3, 4, 5];

        Find::first($items, function ($v) use (&$calls): bool {
            $calls++;

            return $v === 2;
        });

        self::assertSame(2, $calls);
    }

    public function testFirstWithAssociativeArray(): void
    {
        $items = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = Find::first($items, fn($v) => $v === 2);

        self::assertSame(2, $result);
    }

    public function testFirstWithObjects(): void
    {
        $a      = new \stdClass();
        $a->id  = 1;
        $b      = new \stdClass();
        $b->id  = 2;

        $result = Find::first([$a, $b], fn($o) => $o->id === 2);

        self::assertSame($b, $result);
    }

    public function testLastReturnsLastMatch(): void
    {
        $items = [1, 2, 3, 4, 5];

        $result = Find::last($items, fn($v) => $v < 4);

        self::assertSame(3, $result);
    }

    public function testLastReturnsNullWhenNoMatch(): void
    {
        /** @var list<int> $items */
        $items  = [1, 2, 3];
        $result = Find::last($items, fn($v) => $v > 10);

        self::assertNull($result);
    }

    public function testLastReturnsNullOnEmptyArray(): void
    {
        $result = Find::last([], fn($v) => true);

        self::assertNull($result);
    }

    public function testLastWithObjects(): void
    {
        $a      = new \stdClass();
        $a->active = true;
        $b      = new \stdClass();
        $b->active = false;
        $c      = new \stdClass();
        $c->active = true;

        $result = Find::last([$a, $b, $c], fn($o) => $o->active);

        self::assertSame($c, $result);
    }

    public function testFirstAndLastDifferWhenMultipleMatches(): void
    {
        $items = [10, 20, 30, 40, 50];

        $first = Find::first($items, fn($v) => $v % 20 === 0);
        $last  = Find::last($items, fn($v) => $v % 20 === 0);

        self::assertSame(20, $first);
        self::assertSame(40, $last);
    }
}
