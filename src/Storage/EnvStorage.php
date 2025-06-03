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

class EnvStorage implements StorageInterface
{
    public function get(string $key)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($_ENV, $_SERVER, getenv());
    }
}
