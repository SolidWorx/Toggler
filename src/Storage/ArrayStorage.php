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

class ArrayStorage implements StorageInterface
{
    /**
     * @var array<string, bool|string|int|Expression|object|callable|null>
     */
    protected $config;

    /**
     * @param array<string, bool|string|int|Expression|object|callable|null> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }
}
