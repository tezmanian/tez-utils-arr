<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\GroupBy;

final class GroupByTest extends TestCase
{
    public function testGroupsByStringKey(): void
    {
        $users = [
            ['name' => 'Alice', 'role' => 'admin'],
            ['name' => 'Bob', 'role' => 'user'],
            ['name' => 'Carol', 'role' => 'admin'],
        ];

        $result = GroupBy::group($users, fn($u) => $u['role']);

        self::assertSame([
            'admin' => [
                ['name' => 'Alice', 'role' => 'admin'],
                ['name' => 'Carol', 'role' => 'admin'],
            ],
            'user' => [
                ['name' => 'Bob', 'role' => 'user'],
            ],
        ], $result);
    }

    public function testGroupsByIntKey(): void
    {
        $result = GroupBy::group(range(1, 6), fn($n) => $n % 2);

        self::assertSame([
            1 => [1, 3, 5],
            0 => [2, 4, 6],
        ], $result);
    }

    public function testGroupsPreserveDefinitionOrder(): void
    {
        $items  = ['b', 'a', 'b', 'a', 'c'];
        $result = GroupBy::group($items, fn($v) => $v);

        self::assertSame(['b', 'b'], $result['b']);
        self::assertSame(['a', 'a'], $result['a']);
        self::assertSame(['c'], $result['c']);
    }

    public function testEmptyArrayReturnsEmpty(): void
    {
        self::assertSame([], GroupBy::group([], fn($v) => $v));
    }

    public function testSingleGroupWhenAllItemsShareKey(): void
    {
        $result = GroupBy::group([1, 2, 3], fn() => 'same');

        self::assertSame(['same' => [1, 2, 3]], $result);
    }
}
