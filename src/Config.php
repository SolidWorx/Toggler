<?php

/*
 * This file is part of the toggler project.
 *
 * @author    Pierre du Plessis <pdples@gmail.com>
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Toggler;

use SolidWorx\Toggler\Storage\StorageInterface;

class Config
{
    /**
     * @var array|StorageInterface
     */
    private $config;

    /**
     * @var Config
     */
    private static $instance;

    /**
     * @return Config
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    /**
     * @param array|StorageInterface $config
     *
     * @return Config
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Clears the current config
     */
    public function clear()
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
     */
    public function get($value)
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