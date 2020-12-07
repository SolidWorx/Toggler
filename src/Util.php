<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Toggler;

final class Util
{
    /**
     * Checked if a variable has a truthy value.
     *
     * @param mixed $value
     */
    public static function isTruthy($value): bool
    {
        if (\is_bool($value)) {
            return true === $value;
        }

        if (\is_int($value)) {
            return 1 === $value;
        }

        if (\is_string($value)) {
            if (\is_numeric($value) && (int) $value > 0) {
                return 1 === (int) $value;
            }

            return \in_array(\strtolower($value), ['on', 'true', 'yes', 'y'], true);
        }

        return false;
    }
}
