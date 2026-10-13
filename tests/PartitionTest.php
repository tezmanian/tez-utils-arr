<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\Partition;

final class PartitionTest extends TestCase
{
    public function testSplitsIntoPassAndFail(): void
    {
        /** @var list<int> $numbers */
        $numbers = [1, 2, 3, 4, 5, 6];

        [$even, $odd] = Partition::by($numbers, fn($n) => $n % 2 === 0);

        self::assertSame([2, 4, 6], $even);
        self::assertSame([1, 3, 5], $odd);
    }

    public function testAllPassReturnsEmptyFail(): void
    {
        [$pass, $fail] = Partition::by([1, 2, 3], fn() => true);

        self::assertSame([1, 2, 3], $pass);
        self::assertSame([], $fail);
    }

    public function testAllFailReturnsEmptyPass(): void
    {
        [$pass, $fail] = Partition::by([1, 2, 3], fn() => false);

        self::assertSame([], $pass);
        self::assertSame([1, 2, 3], $fail);
    }

    public function testEmptyArrayReturnsTwoEmptyLists(): void
    {
        [$pass, $fail] = Partition::by([], fn() => true);

        self::assertSame([], $pass);
        self::assertSame([], $fail);
    }

    public function testWorksWithAssocArrayItems(): void
    {
        $users = [
            ['name' => 'Alice', 'active' => true],
            ['name' => 'Bob', 'active' => false],
            ['name' => 'Carol', 'active' => true],
        ];

        [$active, $inactive] = Partition::by($users, fn($u) => $u['active']);

        self::assertCount(2, $active);
        self::assertCount(1, $inactive);
        self::assertSame('Alice', $active[0]['name']);
        self::assertSame('Carol', $active[1]['name']);
    }

    public function testResultIsReindexed(): void
    {
        [$pass, $fail] = Partition::by([10, 20, 30, 40], fn($n) => $n > 15);

        self::assertSame(0, array_key_first($pass));
        self::assertSame(0, array_key_first($fail));
    }
}
