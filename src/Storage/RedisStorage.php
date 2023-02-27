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

use InvalidArgumentException;
use Predis\Client;
use Redis;
use RedisArray;
use RedisCluster;

class RedisStorage implements PersistentStorageInterface
{
    /**
     * @var Client|Redis|RedisArray|RedisCluster
     */
    private $redis;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param mixed $redis
     */
    public function __construct($redis, string $namespace = '')
    {
        if (!$redis instanceof Redis && !$redis instanceof RedisArray && !$redis instanceof RedisCluster && !$redis instanceof Client) {
            throw new InvalidArgumentException(sprintf('%s() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, %s given', __METHOD__, is_object($redis) ? get_class($redis) : gettype($redis)));
        }

        $this->redis = $redis;
        $this->namespace = $namespace;
    }

    public function get(string $key)
    {
        return $this->redis->get($this->generateKey($key));
    }

    public function set(string $key, bool $value): bool
    {
        return (bool) $this->redis->set($this->generateKey($key), $value);
    }

    private function generateKey(string $key): string
    {
        return '' !== $this->namespace ? "{$this->namespace}:$key" : $key;
    }
}
