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

class ArrayStorage implements StorageInterface
{
    /**
     * @var array<string, bool|string|int|Expression|object|callable|null>
     */
    protected array $config;

    /**
     * @param array<string, bool|string|int|Expression|object|callable|null> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }

    public function all(): array
    {
        return array_keys($this->config);
    }
}
