<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace Toggler;

class Toggle
{
    /**
     * @var Toggle
     */
    private static $instance;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return Toggle
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Toggle(Config::instance());
        }

        return self::$instance;
    }

    /**
     * @param string $feature
     *
     * @return bool
     */
    public function isActive($feature)
    {
        return isTruthy($this->config->get($feature));
    }

    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function execute(callable $callback)
    {
        return call_user_func($callback);
    }
}