<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\SafeGet;

final class SafeGetTest extends TestCase
{
    /** @var array<mixed> */
    private array $data;

    protected function setUp(): void
    {
        $this->data = [
            'user' => [
                'name'  => 'Alice',
                'age'   => 30,
                'score' => 9.5,
                'active' => true,
                'tags'  => ['admin', 'user'],
            ],
            'chart' => [
                'result' => [
                    [
                        'meta' => ['currency' => 'EUR', 'price' => 123.45],
                        'data' => [10, 20, 30],
                    ],
                ],
            ],
            'empty_string' => '',
            'zero'         => 0,
        ];
    }

    // ── string ────────────────────────────────────────────────────────────────

    public function testStringShallowKey(): void
    {
        self::assertSame('Alice', SafeGet::string($this->data, 'user.name'));
    }

    public function testStringDeeplyNested(): void
    {
        self::assertSame('EUR', SafeGet::string($this->data, 'chart.result.0.meta.currency'));
    }

    public function testStringMissingPathReturnsDefault(): void
    {
        self::assertSame('fallback', SafeGet::string($this->data, 'user.missing', 'fallback'));
    }

    public function testStringMissingPathReturnsNullByDefault(): void
    {
        self::assertNull(SafeGet::string($this->data, 'does.not.exist'));
    }

    public function testStringWrongTypeReturnsDefault(): void
    {
        // age is int, not string
        self::assertNull(SafeGet::string($this->data, 'user.age'));
    }

    // ── int ───────────────────────────────────────────────────────────────────

    public function testIntShallowKey(): void
    {
        self::assertSame(30, SafeGet::int($this->data, 'user.age'));
    }

    public function testIntFromListIndex(): void
    {
        self::assertSame(20, SafeGet::int($this->data, 'chart.result.0.data.1'));
    }

    public function testIntMissingReturnsDefault(): void
    {
        self::assertSame(42, SafeGet::int($this->data, 'user.missing', 42));
    }

    public function testIntWrongTypeReturnsDefault(): void
    {
        // name is string, not int
        self::assertNull(SafeGet::int($this->data, 'user.name'));
    }

    // ── float ─────────────────────────────────────────────────────────────────

    public function testFloatDirectValue(): void
    {
        self::assertSame(9.5, SafeGet::float($this->data, 'user.score'));
    }

    public function testFloatWidensFromInt(): void
    {
        // age is int 30 → widened to 30.0
        self::assertSame(30.0, SafeGet::float($this->data, 'user.age'));
    }

    public function testFloatNestedValue(): void
    {
        self::assertSame(123.45, SafeGet::float($this->data, 'chart.result.0.meta.price'));
    }

    public function testFloatMissingReturnsDefault(): void
    {
        self::assertSame(1.5, SafeGet::float($this->data, 'missing', 1.5));
    }

    // ── bool ──────────────────────────────────────────────────────────────────

    public function testBoolTrue(): void
    {
        self::assertTrue(SafeGet::bool($this->data, 'user.active'));
    }

    public function testBoolMissingReturnsDefault(): void
    {
        self::assertFalse(SafeGet::bool($this->data, 'user.missing', false));
    }

    public function testBoolWrongTypeReturnsDefault(): void
    {
        // integer 1 is not a bool
        self::assertNull(SafeGet::bool($this->data, 'user.age'));
    }

    // ── array ─────────────────────────────────────────────────────────────────

    public function testArrayShallowKey(): void
    {
        self::assertSame(['admin', 'user'], SafeGet::array($this->data, 'user.tags'));
    }

    public function testArrayNestedObject(): void
    {
        $result = SafeGet::array($this->data, 'chart.result.0');
        self::assertIsArray($result);
        self::assertArrayHasKey('meta', $result);
    }

    public function testArrayMissingReturnsDefault(): void
    {
        self::assertSame(['x'], SafeGet::array($this->data, 'missing', ['x']));
    }

    public function testArrayMissingReturnsNullByDefault(): void
    {
        self::assertNull(SafeGet::array($this->data, 'user.name'));
    }

    // ── edge cases ────────────────────────────────────────────────────────────

    public function testMidPathNotArrayReturnsDefault(): void
    {
        // 'user.name' is a string; trying to go deeper must return null
        self::assertNull(SafeGet::string($this->data, 'user.name.nested'));
    }

    public function testNumericStringKeyAddressesListIndex(): void
    {
        self::assertSame(10, SafeGet::int($this->data, 'chart.result.0.data.0'));
        self::assertSame(30, SafeGet::int($this->data, 'chart.result.0.data.2'));
    }

    public function testEmptyPathSegmentsAreHandled(): void
    {
        $flat = ['key' => 'value'];
        self::assertSame('value', SafeGet::string($flat, 'key'));
    }

    public function testRootLevelArrayAccess(): void
    {
        $data = ['name' => 'Bob'];
        self::assertSame('Bob', SafeGet::string($data, 'name'));
    }
}
