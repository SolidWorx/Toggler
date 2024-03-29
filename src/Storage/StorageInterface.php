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

namespace SolidWorx\Toggler\Storage;

use Symfony\Component\ExpressionLanguage\Expression;

interface StorageInterface
{
    /**
     * Reads a key from the storage.
     *
     * @return bool|string|int|Expression|object|callable|null
     */
    public function get(string $key);

    /**
     * Returns a list of all the configured features.
     *
     * @return array<int|string, mixed>
     */
    public function all(): array;
}
