<?php

declare(strict_types=1);

namespace Tez\Utils\Arr;

/**
 * Represents a single difference between two arrays at a specific dot-notation path.
 */
final readonly class Change
{
    public function __construct(
        /** Dot-notation path to the changed key, e.g. "user.address.city". */
        public readonly string $path,
        /** Whether the key was added, removed, or its value changed. */
        public readonly ChangeType $type,
        /** The value in the old array, or null when the key was added. */
        public readonly mixed $old,
        /** The value in the new array, or null when the key was removed. */
        public readonly mixed $new,
    ) {}
}
