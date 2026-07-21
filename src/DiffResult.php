<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

/**
 * Immutable result of an ArrayDiff comparison.
 */
final readonly class DiffResult
{
    /**
     * @param list<Change> $changes
     */
    public function __construct(
        private array $changes,
    ) {}

    public function hasChanges(): bool
    {
        return $this->changes !== [];
    }

    /** @return list<Change> */
    public function changes(): array
    {
        return $this->changes;
    }

    /** @return list<string> */
    public function paths(): array
    {
        return array_values(array_map(
            static fn(Change $c): string => $c->path,
            $this->changes,
        ));
    }
}
