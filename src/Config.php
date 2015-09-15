<?php

/*
 * This file is part of the toggler project.
 *
 * @author    Pierre du Plessis <pdples@gmail.com>
 * @copyright Copyright (c) 2015
 */

namespace Toggler;

class Config
{
    /**
     * @var array
     */
    private $config = [];

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
     * @param array $config
     *
     * @return Config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

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
        if (array_key_exists($value, $this->config)) {
            return $this->config[$value];
        }

        throw new \InvalidArgumentException(sprintf('The config "%s" does not exist', $value));
    }
}