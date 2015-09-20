<?php

use Symfony\Component\Yaml\Yaml;
use Toggler\Config;

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
 * @param array         $context
 *
 * @return mixed
 */
function toggle($feature, $callback = null, $reverseCallback = null, $context = [])
{
    $toggler = Toggler\Toggle::instance();

    if (empty($context) && is_array($callback) && !is_callable($callback)) {
        $context = $callback;
        $callback = null;
    }

    if (empty($context) && is_array($reverseCallback) && !is_callable($reverseCallback)) {
        $context = $reverseCallback;
        $reverseCallback = null;
    }

    $active = $toggler->isActive($feature, $context);

    if (null === $callback) {
        return $active;
    }

    if ($active) {
        return $toggler->execute($callback);
    } elseif (null !== $reverseCallback) {
        return $toggler->execute($reverseCallback);
    }
}