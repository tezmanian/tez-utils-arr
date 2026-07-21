<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\UniqueBy;

final class UniqueByTest extends TestCase
{
    public function testFirstWinsByDefault(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 1, 'name' => 'Alice2'],
        ];

        $result = UniqueBy::filter($items, fn($item) => $item['id']);

        self::assertCount(2, $result);
        self::assertSame('Alice', $result[0]['name']);
    }

    public function testLastWins(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 1, 'name' => 'Alice2'],
        ];

        $result = UniqueBy::filter($items, fn($item) => $item['id'], lastWins: true);

        // Bob's only occurrence is at position 1, Alice2's last occurrence is at position 2
        // → order reflects last-occurrence position in the original array
        self::assertCount(2, $result);
        self::assertSame('Bob', $result[0]['name']);
        self::assertSame('Alice2', $result[1]['name']);
    }

    public function testLastWinsPreservesOriginalOrder(): void
    {
        $items = [
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'name' => 'B'],
            ['id' => 1, 'name' => 'C'],
            ['id' => 2, 'name' => 'D'],
        ];

        $result = UniqueBy::filter($items, fn($item) => $item['id'], lastWins: true);

        self::assertCount(2, $result);
        self::assertSame('C', $result[0]['name']);
        self::assertSame('D', $result[1]['name']);
    }

    public function testReIndexedByDefault(): void
    {
        $items = [10 => 'a', 20 => 'b', 30 => 'a'];

        $result = UniqueBy::filter($items, fn($v) => $v);

        self::assertSame([0 => 'a', 1 => 'b'], $result);
    }

    public function testPreserveKeys(): void
    {
        $items = [10 => 'a', 20 => 'b', 30 => 'a'];

        $result = UniqueBy::filter($items, fn($v) => $v, preserveKeys: true);

        self::assertArrayHasKey(10, $result);
        self::assertArrayHasKey(20, $result);
        self::assertArrayNotHasKey(30, $result);
    }

    public function testPreserveKeysWithLastWins(): void
    {
        $items = [10 => 'a', 20 => 'b', 30 => 'a'];

        $result = UniqueBy::filter($items, fn($v) => $v, lastWins: true, preserveKeys: true);

        self::assertArrayNotHasKey(10, $result);
        self::assertArrayHasKey(20, $result);
        self::assertArrayHasKey(30, $result);
    }

    public function testEmptyArrayReturnsEmpty(): void
    {
        $result = UniqueBy::filter([], fn($v) => $v);

        self::assertSame([], $result);
    }

    public function testAllUniqueReturnsAllItems(): void
    {
        $items = [1, 2, 3, 4];

        $result = UniqueBy::filter($items, fn($v) => $v);

        self::assertSame([1, 2, 3, 4], $result);
    }

    public function testAllDuplicatesReturnsSingleItem(): void
    {
        $items = ['a', 'a', 'a'];

        $result = UniqueBy::filter($items, fn($v) => $v);

        self::assertSame(['a'], $result);
    }

    public function testCallbackTransformation(): void
    {
        $items = ['Alice', 'alice', 'BOB', 'Bob'];

        $result = UniqueBy::filter($items, fn($v) => strtolower($v));

        self::assertSame(['Alice', 'BOB'], $result);
    }

    public function testCallbackOnObjects(): void
    {
        $a      = new \stdClass();
        $a->id  = 1;
        $b      = new \stdClass();
        $b->id  = 2;
        $c      = new \stdClass();
        $c->id  = 1;

        $result = UniqueBy::filter([$a, $b, $c], fn($o) => $o->id);

        self::assertCount(2, $result);
        self::assertSame($a, $result[0]);
        self::assertSame($b, $result[1]);
    }
}
