<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class ArrayDiff
{
    /**
     * Recursively compares two arrays and returns a DiffResult with all differences.
     * Uses strict equality (1 !== "1"). Paths are reported in dot-notation.
     *
     * @param array<mixed> $old
     * @param array<mixed> $new
     */
    public static function compare(array $old, array $new): DiffResult
    {
        $changes = [];
        self::diff($old, $new, '', $changes);

        return new DiffResult($changes);
    }

    /**
     * @param array<mixed> $old
     * @param array<mixed> $new
     * @param list<Change> $changes
     */
    private static function diff(array $old, array $new, string $prefix, array &$changes): void
    {
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($allKeys as $key) {
            $path = $prefix !== '' ? $prefix . '.' . $key : (string) $key;

            $inOld = array_key_exists($key, $old);
            $inNew = array_key_exists($key, $new);

            if ($inOld && !$inNew) {
                $changes[] = new Change($path, ChangeType::Removed, $old[$key], null);
                continue;
            }

            if (!$inOld && $inNew) {
                $changes[] = new Change($path, ChangeType::Added, null, $new[$key]);
                continue;
            }

            $oldVal = $old[$key];
            $newVal = $new[$key];

            if (is_array($oldVal) && is_array($newVal)) {
                self::diff($oldVal, $newVal, $path, $changes);
                continue;
            }

            if ($oldVal !== $newVal) {
                $changes[] = new Change($path, ChangeType::Changed, $oldVal, $newVal);
            }
        }
    }
}
