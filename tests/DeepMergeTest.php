<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tezmanian\Utils\Arr\DeepMerge;

final class DeepMergeTest extends TestCase
{
    public function testMergesTopLevelScalars(): void
    {
        $result = DeepMerge::merge(
            ['a' => 1, 'b' => 2],
            ['b' => 99, 'c' => 3],
        );

        self::assertSame(['a' => 1, 'b' => 99, 'c' => 3], $result);
    }

    public function testMergesNestedArraysRecursively(): void
    {
        $result = DeepMerge::merge(
            ['db' => ['host' => 'localhost', 'port' => 3306]],
            ['db' => ['host' => 'db.prod']],
        );

        self::assertSame(['db' => ['host' => 'db.prod', 'port' => 3306]], $result);
    }

    public function testBaseOnlyKeysAreKept(): void
    {
        $result = DeepMerge::merge(
            ['a' => 1, 'b' => 2],
            ['b' => 99],
        );

        self::assertSame(1, $result['a']);
    }

    public function testOverrideOnlyKeysAreAdded(): void
    {
        $result = DeepMerge::merge(['a' => 1], ['b' => 2]);

        self::assertSame(['a' => 1, 'b' => 2], $result);
    }

    public function testNullOverwrites(): void
    {
        $result = DeepMerge::merge(['a' => 'value'], ['a' => null]);

        self::assertNull($result['a']);
    }

    public function testIntegerKeysAreAppended(): void
    {
        $result = DeepMerge::merge([1, 2, 3], [4, 5]);

        self::assertSame([1, 2, 3, 4, 5], $result);
    }

    public function testMultipleOverridesAppliedLeftToRight(): void
    {
        $result = DeepMerge::merge(
            ['a' => 1],
            ['a' => 2],
            ['a' => 3],
        );

        self::assertSame(3, $result['a']);
    }

    public function testDeepNestedMerge(): void
    {
        $defaults = [
            'cache' => ['ttl' => 3600, 'driver' => 'file'],
            'debug' => false,
        ];
        $env = [
            'cache' => ['driver' => 'redis'],
            'debug' => true,
        ];

        $result = DeepMerge::merge($defaults, $env);

        /** @var array<string, mixed> $cache */
        $cache = $result['cache'];
        self::assertSame(3600, $cache['ttl']);
        self::assertSame('redis', $cache['driver']);
        self::assertTrue($result['debug']);
    }

    public function testOverridingArrayWithScalarWins(): void
    {
        $result = DeepMerge::merge(
            ['key' => ['nested' => 1]],
            ['key' => 'scalar'],
        );

        self::assertSame('scalar', $result['key']);
    }

    public function testEmptyOverrideReturnsBase(): void
    {
        $base = ['a' => 1, 'b' => 2];
        self::assertSame($base, DeepMerge::merge($base));
    }
}
