<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

final class Transpose
{
    /**
     * Swaps rows and columns of a 2D array.
     * All rows must have the same number of columns.
     * Associative keys are stripped; result is always a list of lists.
     *
     * @param array<array<mixed>> $rows
     * @throws \InvalidArgumentException when rows have unequal column counts
     * @return list<list<mixed>>
     */
    public static function matrix(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        $rows     = array_values(array_map('array_values', $rows));
        $colCount = count($rows[0]);

        foreach ($rows as $row) {
            if (count($row) !== $colCount) {
                throw new \InvalidArgumentException(
                    'All rows must have the same number of columns.',
                );
            }
        }

        $result = [];

        for ($col = 0; $col < $colCount; $col++) {
            $column = [];

            foreach ($rows as $row) {
                $column[] = $row[$col];
            }

            $result[] = $column;
        }

        return $result;
    }
}
