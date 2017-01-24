<?php

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Toggler\Storage;

interface StorageInterface
{
    /**
     * Reads a key from the storage
     *
     * @param string $key
     *
     * @return bool|string|int
     */
    public function get(string $key);
}