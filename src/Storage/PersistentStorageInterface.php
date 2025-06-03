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

interface PersistentStorageInterface extends StorageInterface
{
    /**
     * Update a value in the storage.
     *
     * @return bool|string|int
     */
    public function set(string $key, bool $value);
}
