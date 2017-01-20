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

namespace SolidWorx\Toggler;

use SolidWorx\Toggler\Storage\ArrayStorage;
use SolidWorx\Toggler\Storage\StorageInterface;
use SolidWorx\Toggler\Storage\YamlFileStorage;

class Config implements StorageInterface
{
    /**
     * @var array|StorageInterface
     */
    private $config;

    /**
     * @param mixed $config
     *
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->config = self::factory($config);
    }

    /**
     * @param $config
     *
     * @return StorageInterface
     * @throws \InvalidArgumentException
     */
    public static function factory($config): StorageInterface
    {
        switch (true) {
            case $config instanceof StorageInterface:
                return $config;
                break;
            case is_array($config):
                return new ArrayStorage($config);
                break;
            case is_file($config):
                if ('yml' === pathinfo($config, PATHINFO_EXTENSION)) {
                    return new YamlFileStorage($config);
                } else {
                    return new ArrayStorage(require_once $config);
                }
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The 1st argument for %s expects an array, string or instance of StorageInterface, %s given', __METHOD__, is_object($config) ? get_class($config) : gettype($config)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $value)
    {
        return $this->config->get($value);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, bool $value)
    {
        return $this->config->set($key, $value);
    }
}