<?php

/*
 * This file is part of the toggler project.
 *
 * @author    pierre
 * @copyright Copyright (c) 2015
 */

namespace SolidWorx\Toggler;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

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
     * @var array
     */
    private $callback = [];

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        if (class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
            $this->expressionLanguage = new ExpressionLanguage();
        }
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
        $value = $this->config->get($feature);

        switch (true) {
            case $value instanceof Expression:
                $value = $this->evaluateExpression($feature, $value, $context);
                break;
            case is_callable($value):
                $value = $this->evaluateCallback($feature, $value, $context);
                break;
        }

        return $this->isTruthy($value);
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
     *
     * @return bool
     */
    private function isTruthy($value)
    {
        if (is_bool($value)) {
            return true === $value;
        }

        if (is_int($value)) {
            return 1 === $value;
        }

        if (is_string($value)) {
            if ((int) $value > 0) {
                return 1 === (int) $value;
            }

            return in_array(strtolower($value), ['on', 'true'], true);
        }

        return false;
    }

    /**
     * @param string $feature
     * @param array  $context
     *
     * @return string
     */
    private function generateKey($feature, array $context)
    {
        return serialize(['feature' => $feature, 'context' => $context]);
    }

    /**
     * @param string $feature
     * @param mixed  $value
     * @param array  $context
     *
     * @return array
     */
    private function evaluateExpression($feature, $value, array $context)
    {
        $key = $this->generateKey($feature, $context);

        if (array_key_exists($key, $this->callback)) {
            return $this->callback[$key];
        }

        $value = $this->expressionLanguage->evaluate($value, $context);
        $this->callback[$key] = $value;

        return $value;
    }

    /**
     * @param string $feature
     * @param mixed  $value
     * @param array  $context
     *
     * @return mixed
     */
    private function evaluateCallback($feature, $value, array $context)
    {
        $key = $this->generateKey($feature, $context);
        if (array_key_exists($key, $this->callback)) {
            return $this->callback[$key];
        }

        $value = call_user_func_array($value, $context);
        $this->callback[$key] = $value;

        return $value;
    }
}