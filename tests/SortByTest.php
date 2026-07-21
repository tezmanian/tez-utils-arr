<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\SortBy;

final class SortByTest extends TestCase
{
    public function testSortsByIntKeyAscending(): void
    {
        $users = [
            ['name' => 'Carol', 'age' => 32],
            ['name' => 'Alice', 'age' => 28],
            ['name' => 'Bob', 'age' => 45],
        ];

        $result = SortBy::sort($users, fn($u) => $u['age']);

        self::assertSame('Alice', $result[0]['name']);
        self::assertSame('Carol', $result[1]['name']);
        self::assertSame('Bob', $result[2]['name']);
    }

    public function testSortsByIntKeyDescending(): void
    {
        $users = [
            ['name' => 'Carol', 'age' => 32],
            ['name' => 'Alice', 'age' => 28],
            ['name' => 'Bob', 'age' => 45],
        ];

        $result = SortBy::sort($users, fn($u) => $u['age'], descending: true);

        self::assertSame('Bob', $result[0]['name']);
        self::assertSame('Carol', $result[1]['name']);
        self::assertSame('Alice', $result[2]['name']);
    }

    public function testSortsByStringKey(): void
    {
        $items = ['banana', 'apple', 'cherry'];

        $result = SortBy::sort($items, fn($v) => $v);

        self::assertSame(['apple', 'banana', 'cherry'], $result);
    }

    public function testDoesNotMutateOriginal(): void
    {
        $original = [3, 1, 2];
        SortBy::sort($original, fn($v) => $v);

        self::assertSame([3, 1, 2], $original);
    }

    public function testResultIsReindexed(): void
    {
        $result = SortBy::sort([3, 1, 2], fn($v) => $v);

        self::assertSame(0, array_key_first($result));
        self::assertSame(1, $result[0]);
    }

    public function testEmptyArrayReturnsEmpty(): void
    {
        self::assertSame([], SortBy::sort([], fn($v) => $v));
    }

    public function testStableSortPreservesEqualOrder(): void
    {
        $items = [
            ['name' => 'A', 'age' => 30],
            ['name' => 'B', 'age' => 30],
        ];

        $result = SortBy::sort($items, fn($u) => $u['age']);

        // Equal keys: original order must be preserved (PHP's usort is stable since 8.0)
        self::assertSame('A', $result[0]['name']);
        self::assertSame('B', $result[1]['name']);
    }
}
