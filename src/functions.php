<?php

use Toggler\Config;
use Symfony\Component\Yaml\Yaml;

/**
 * @param array $features
 *
 * @return Config
 * @throws Exception
 */
function toggleConfig($features)
{
    $config = Config::instance();

    if (is_array($features)) {
        return $config->setConfig($features);
    }

    if (is_file($file = realpath($features))) {
        if ('yml' === pathinfo($file, PATHINFO_EXTENSION)) {
            if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                throw new \Exception('The Symfony Yaml component is needed in order to load config from yml filed');
            }

            return $config->setConfig(Yaml::parse(file_get_contents($file)));
        }

        $values = require_once $file;

        return $config->setConfig($values);
    }
}

/**
 * @param string        $feature
 * @param callable      $callback
 * @param callable|null $reverseCallback
 *
 * @return mixed
 */
function toggle($feature, callable $callback = null, callable $reverseCallback = null)
{
    $toggler = Toggler\Toggle::instance();

    $active = $toggler->isActive($feature);

    if (null === $callback) {
        return $active;
    }

    if ($active) {
        return $toggler->execute($callback);
    } elseif (null !== $reverseCallback) {
        return $toggler->execute($reverseCallback);
    }
}

/**
 * Checked if a variable has a truthy value
 *
 * @param mixed $value
 *
 * @return bool
 */
function isTruthy($value)
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