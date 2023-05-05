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

class EnvStorage implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($_ENV, $_SERVER, getenv());
    }
}
