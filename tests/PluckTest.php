<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\Pluck;

final class PluckTest extends TestCase
{
    public function testPlucksTopLevelKey(): void
    {
        $items = [
            ['name' => 'Alice', 'age' => 28],
            ['name' => 'Bob', 'age' => 34],
        ];

        self::assertSame(['Alice', 'Bob'], Pluck::values($items, 'name'));
    }

    public function testPlucksDotNotationKey(): void
    {
        $items = [
            ['id' => 1, 'profile' => ['name' => 'Alice']],
            ['id' => 2, 'profile' => ['name' => 'Bob']],
        ];

        self::assertSame(['Alice', 'Bob'], Pluck::values($items, 'profile.name'));
    }

    public function testSkipsMissingKeys(): void
    {
        $items = [
            ['name' => 'Alice'],
            ['age'  => 30],
            ['name' => 'Carol'],
        ];

        self::assertSame(['Alice', 'Carol'], Pluck::values($items, 'name'));
    }

    public function testKeyedByReturnsAssocArray(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        self::assertSame([1 => 'Alice', 2 => 'Bob'], Pluck::keyedBy($items, 'name', 'id'));
    }

    public function testKeyedByWithDotNotation(): void
    {
        $items = [
            ['id' => 1, 'profile' => ['name' => 'Alice']],
            ['id' => 2, 'profile' => ['name' => 'Bob']],
        ];

        self::assertSame([1 => 'Alice', 2 => 'Bob'], Pluck::keyedBy($items, 'profile.name', 'id'));
    }

    public function testEmptyInputReturnsEmpty(): void
    {
        self::assertSame([], Pluck::values([], 'name'));
        self::assertSame([], Pluck::keyedBy([], 'name', 'id'));
    }

    public function testPlucksNullValues(): void
    {
        $items = [
            ['name' => null],
            ['name' => 'Bob'],
        ];

        self::assertSame([null, 'Bob'], Pluck::values($items, 'name'));
    }
}
