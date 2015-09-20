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
     * @param array  $context
     *
     * @return bool
     */
    public function isActive($feature, array $context = [])
    {
        return $this->isTruthy($this->config->get($feature), $context);
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

    /**
     * Checked if a variable has a truthy value
     *
     * @param mixed $value
     * @param array $context
     *
     * @return bool
     */
    private function isTruthy($value, array $context = [])
    {
        if (is_bool($value)) {
            return true === $value;
        }

        if (is_int($value)) {
            return 1 === $value;
        }

        if (is_callable($value)) {
            return $this->isTruthy(call_user_func_array($value, $context));
        }

        if (is_string($value)) {
            if ((int) $value > 0) {
                return 1 === (int) $value;
            }

            return in_array(strtolower($value), ['on', 'true'], true);
        }

        return false;
    }
}