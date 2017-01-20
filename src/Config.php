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

use SolidWorx\Toggler\Storage\StorageInterface;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var array|StorageInterface
     */
    private $config;

    /**
     * @param array|StorageInterface|string $config
     *
     * @throws \Exception
     */
    public function __construct($config)
    {
        if (is_array($config) || $config instanceof StorageInterface) {
            $this->config = $config;
        } else if (is_file($file = realpath($config))) {
            if ('yml' === pathinfo($file, PATHINFO_EXTENSION)) {
                if (!class_exists(Yaml::class)) {
                    throw new \Exception('The Symfony Yaml component is needed in order to load config from yml file');
                }

                $this->config = Yaml::parse(file_get_contents($file));
            } else {
                $this->config = require_once $file;
            }
        }
    }

    /**
     * Clears the current config
     */
    public function clear(): Config
    {
        $this->config = null;

        return $this;
    }

    /**
     * Get a config value
     *
     * @param string $value
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get(string $value)
    {
        if (is_array($this->config) && array_key_exists($value, $this->config)) {
            return $this->config[$value];
        }

        if ($this->config instanceof StorageInterface) {
            return $this->config->get($value);
        }

        throw new \InvalidArgumentException(sprintf('The config "%s" does not exist', $value));
    }
}