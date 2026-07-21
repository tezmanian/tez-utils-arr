<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\ArrayDiff;
use Tez\Utils\Arr\ChangeType;
use Tez\Utils\Arr\DiffResult;

final class ArrayDiffTest extends TestCase
{
    public function testIdenticalArraysHaveNoChanges(): void
    {
        $result = ArrayDiff::compare(['a' => 1], ['a' => 1]);

        self::assertInstanceOf(DiffResult::class, $result);
        self::assertFalse($result->hasChanges());
        self::assertSame([], $result->changes());
        self::assertSame([], $result->paths());
    }

    public function testDetectsChangedScalarValue(): void
    {
        $result = ArrayDiff::compare(
            ['user' => ['name' => 'Anna', 'city' => 'Berlin']],
            ['user' => ['name' => 'Anna', 'city' => 'Hamburg']],
        );

        self::assertTrue($result->hasChanges());
        self::assertCount(1, $result->changes());

        $change = $result->changes()[0];
        self::assertSame('user.city', $change->path);
        self::assertSame(ChangeType::Changed, $change->type);
        self::assertSame('Berlin', $change->old);
        self::assertSame('Hamburg', $change->new);
        self::assertSame(['user.city'], $result->paths());
    }

    public function testDetectsAddedKey(): void
    {
        $result = ArrayDiff::compare(
            ['a' => 1],
            ['a' => 1, 'b' => 2],
        );

        self::assertTrue($result->hasChanges());
        $change = $result->changes()[0];
        self::assertSame('b', $change->path);
        self::assertSame(ChangeType::Added, $change->type);
        self::assertNull($change->old);
        self::assertSame(2, $change->new);
    }

    public function testDetectsRemovedKey(): void
    {
        $result = ArrayDiff::compare(
            ['a' => 1, 'b' => 2],
            ['a' => 1],
        );

        self::assertTrue($result->hasChanges());
        $change = $result->changes()[0];
        self::assertSame('b', $change->path);
        self::assertSame(ChangeType::Removed, $change->type);
        self::assertSame(2, $change->old);
        self::assertNull($change->new);
    }

    public function testDeepNestedPath(): void
    {
        $result = ArrayDiff::compare(
            ['a' => ['b' => ['c' => 'old']]],
            ['a' => ['b' => ['c' => 'new']]],
        );

        self::assertSame(['a.b.c'], $result->paths());
        self::assertSame(ChangeType::Changed, $result->changes()[0]->type);
    }

    public function testStrictTypeCheckIntVsString(): void
    {
        $result = ArrayDiff::compare(['x' => 1], ['x' => '1']);

        self::assertTrue($result->hasChanges());
        self::assertSame(ChangeType::Changed, $result->changes()[0]->type);
    }

    public function testStrictTypeCheckIntVsTrue(): void
    {
        $result = ArrayDiff::compare(['x' => 1], ['x' => true]);

        self::assertTrue($result->hasChanges());
    }

    public function testMultipleDifferencesReported(): void
    {
        $result = ArrayDiff::compare(
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 1, 'b' => 99, 'c' => 3, 'd' => 4],
        );

        self::assertCount(2, $result->changes());
        self::assertContains('b', $result->paths());
        self::assertContains('d', $result->paths());
    }

    public function testNestedAddedAndRemovedKeys(): void
    {
        $result = ArrayDiff::compare(
            ['user' => ['name' => 'Anna', 'age' => 30]],
            ['user' => ['name' => 'Anna', 'email' => 'anna@example.com']],
        );

        $paths = $result->paths();
        self::assertContains('user.age', $paths);
        self::assertContains('user.email', $paths);

        $types = array_map(
            static fn($c) => $c->type,
            $result->changes(),
        );
        self::assertContains(ChangeType::Removed, $types);
        self::assertContains(ChangeType::Added, $types);
    }

    public function testEmptyArraysProduceNoDiff(): void
    {
        $result = ArrayDiff::compare([], []);

        self::assertFalse($result->hasChanges());
    }

    public function testScalarToArrayIsReportedAsChange(): void
    {
        $result = ArrayDiff::compare(
            ['x' => 'scalar'],
            ['x' => ['nested' => 'value']],
        );

        self::assertTrue($result->hasChanges());
        self::assertSame(ChangeType::Changed, $result->changes()[0]->type);
    }

    public function testArrayToScalarIsReportedAsChange(): void
    {
        $result = ArrayDiff::compare(
            ['x' => ['nested' => 'value']],
            ['x' => 'scalar'],
        );

        self::assertTrue($result->hasChanges());
        self::assertSame(ChangeType::Changed, $result->changes()[0]->type);
    }
}
