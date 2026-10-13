<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\MapWithKeys;

final class MapWithKeysTest extends TestCase
{
    public function testTransformsKeysAndValues(): void
    {
        /** @var list<array{id: int, name: string}> $users */
        $users = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $result = MapWithKeys::map($users, function (mixed $u) {
            /** @var array{id: int, name: string} $u */
            return [$u['id'] => $u['name']];
        });

        self::assertSame([1 => 'Alice', 2 => 'Bob'], $result);
    }

    public function testSwapsKeyAndValue(): void
    {
        $result = MapWithKeys::map(['a' => 1, 'b' => 2], function (mixed $v, int|string $k) {
            /** @var int $v */
            return [$v => $k];
        });

        self::assertSame([1 => 'a', 2 => 'b'], $result);
    }

    public function testLastWinsOnDuplicateKey(): void
    {
        $result = MapWithKeys::map([1, 2, 3], fn($v) => ['key' => $v]);

        self::assertSame(['key' => 3], $result);
    }

    public function testEmptyInputReturnsEmpty(): void
    {
        self::assertSame([], MapWithKeys::map([], fn($v) => ['x' => $v]));
    }

    public function testReceivesOriginalKey(): void
    {
        $received = [];
        MapWithKeys::map(['x' => 10, 'y' => 20], function ($v, $k) use (&$received) {
            $received[] = $k;

            return [$k => $v];
        });

        self::assertSame(['x', 'y'], $received);
    }
}
