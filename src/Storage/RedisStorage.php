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

class RedisStorage implements PersistenStorageInterface
{
    /**
     * @var \Predis\Client|\Redis|\RedisArray|\RedisCluster
     */
    private $redis;

    public function __construct($redis)
    {
        if (!$redis instanceof \Redis && !$redis instanceof \RedisArray && !$redis instanceof \RedisCluster && !$redis instanceof \Predis\Client) {
            throw new \InvalidArgumentException(sprintf('%s() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, %s given', __METHOD__, is_object($redis) ? get_class($redis) : gettype($redis)));
        }

        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, bool $value)
    {
        return $this->redis->set($key, $value);
    }
}
