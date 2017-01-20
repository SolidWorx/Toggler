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

class ArrayStorage implements StorageInterface
{
    /**
     * @var array
     */
    protected $config;

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

    /**
     * {@inheritdoc}
     */
    public function set(string $key, bool $value)
    {
        $this->config[$key] = $value;
    }
}