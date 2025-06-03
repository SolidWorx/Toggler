<?php

declare(strict_types=1);

/*
 * This file is part of SolidWorx Toggler project.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidWorx\Toggler;

use function in_array;
use function is_bool;
use function is_int;
use function is_numeric;
use function is_string;
use function strtolower;

final class Util
{
    /**
     * Checked if a variable has a truthy value.
     *
     * @param mixed $value
     */
    public static function isTruthy($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            if (is_numeric($value) && (int) $value > 0) {
                return (int) $value === 1;
            }

            return in_array(strtolower($value), ['on', 'true', 'yes', 'y'], true);
        }

        return false;
    }
}
