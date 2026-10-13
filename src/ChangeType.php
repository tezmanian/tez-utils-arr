<?php

declare(strict_types=1);

namespace Tezmanian\Utils\Arr;

use Tezmanian\Utils\Enum\BackedEnumTrait;

/**
 * Classifies the type of a single difference detected by ArrayDiff.
 */
enum ChangeType: string
{
    use BackedEnumTrait;

    case Added   = 'added';
    case Removed = 'removed';
    case Changed = 'changed';
}
