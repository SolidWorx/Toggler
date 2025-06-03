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
