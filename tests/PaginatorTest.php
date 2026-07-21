<?php

declare(strict_types=1);

namespace Tez\Utils\Tests\Arr;

use PHPUnit\Framework\TestCase;
use Tez\Utils\Arr\Page;
use Tez\Utils\Arr\Paginator;

final class PaginatorTest extends TestCase
{
    /** @return list<int> */
    private function range100(): array
    {
        return range(1, 100);
    }

    public function testFirstPageReturnsCorrectSlice(): void
    {
        $page = Paginator::paginate($this->range100(), page: 1, perPage: 10);

        self::assertInstanceOf(Page::class, $page);
        self::assertSame(range(1, 10), $page->items());
        self::assertSame(100, $page->total());
        self::assertSame(1, $page->currentPage());
        self::assertSame(10, $page->lastPage());
    }

    public function testSecondPageReturnsCorrectSlice(): void
    {
        $page = Paginator::paginate($this->range100(), page: 2, perPage: 10);

        self::assertSame(range(11, 20), $page->items());
        self::assertSame(2, $page->currentPage());
    }

    public function testLastPageHasFewerItems(): void
    {
        $items = range(1, 25);
        $page  = Paginator::paginate($items, page: 3, perPage: 10);

        self::assertSame([21, 22, 23, 24, 25], $page->items());
        self::assertSame(3, $page->lastPage());
        self::assertFalse($page->hasNext());
        self::assertTrue($page->hasPrev());
    }

    public function testHasNextAndHasPrev(): void
    {
        $items = range(1, 30);

        $first = Paginator::paginate($items, page: 1, perPage: 10);
        self::assertTrue($first->hasNext());
        self::assertFalse($first->hasPrev());

        $middle = Paginator::paginate($items, page: 2, perPage: 10);
        self::assertTrue($middle->hasNext());
        self::assertTrue($middle->hasPrev());

        $last = Paginator::paginate($items, page: 3, perPage: 10);
        self::assertFalse($last->hasNext());
        self::assertTrue($last->hasPrev());
    }

    public function testEmptyArrayReturnsEmptyPage(): void
    {
        $page = Paginator::paginate([], page: 1, perPage: 10);

        self::assertSame([], $page->items());
        self::assertSame(0, $page->total());
        self::assertSame(1, $page->lastPage());
        self::assertFalse($page->hasNext());
        self::assertFalse($page->hasPrev());
    }

    public function testPageBeyondLastPageReturnsEmptyItems(): void
    {
        $page = Paginator::paginate(range(1, 5), page: 10, perPage: 10);

        self::assertSame([], $page->items());
    }

    public function testPageZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Paginator::paginate([], page: 0, perPage: 10);
    }

    public function testNegativePageThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Paginator::paginate([], page: -1, perPage: 10);
    }

    public function testPerPageZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Paginator::paginate([], page: 1, perPage: 0);
    }

    public function testPerPageNegativeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Paginator::paginate([], page: 1, perPage: -5);
    }

    public function testItemsAreReIndexed(): void
    {
        $items = ['a' => 'x', 'b' => 'y', 'c' => 'z'];
        $page  = Paginator::paginate(array_values($items), page: 1, perPage: 2);

        self::assertSame(['x', 'y'], $page->items());
    }
}
