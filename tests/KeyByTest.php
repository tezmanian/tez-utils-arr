<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\KeyBy;

final class KeyByTest extends TestCase
{
    public function testIndexByStringKey(): void
    {
        $users = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $result = KeyBy::index($users, fn($u) => $u['name']);

        self::assertSame([
            'Alice' => ['id' => 1, 'name' => 'Alice'],
            'Bob'   => ['id' => 2, 'name' => 'Bob'],
        ], $result);
    }

    public function testIndexByIntKey(): void
    {
        $users = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $result = KeyBy::index($users, fn($u) => $u['id']);

        self::assertSame(1, array_key_first($result));
        self::assertSame('Alice', $result[1]['name']);
    }

    public function testLastItemWinsOnDuplicateKey(): void
    {
        $items = [
            ['key' => 'x', 'val' => 1],
            ['key' => 'x', 'val' => 2],
        ];

        $result = KeyBy::index($items, fn($i) => $i['key']);

        self::assertSame(2, $result['x']['val']);
    }

    public function testEmptyInputReturnsEmpty(): void
    {
        self::assertSame([], KeyBy::index([], fn($v) => $v));
    }

    public function testFullItemIsPreserved(): void
    {
        $items = [['id' => 1, 'name' => 'Alice', 'score' => 95]];

        $result = KeyBy::index($items, fn($i) => $i['id']);

        self::assertSame(['id' => 1, 'name' => 'Alice', 'score' => 95], $result[1]);
    }
}
