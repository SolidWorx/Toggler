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

use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Storage\StorageInterface;
use SolidWorx\Toggler\Toggle;

/**
 * @param string|array|StorageInterface $features
 *
 * @return Config
 * @throws Exception
 */
function toggleConfig($features = null): Config
{
    static $config;

    if (!$config) {
        $config = new Config($features);
    }

    return $config;
}

/**
 * @param string        $feature
 * @param callable      $callback
 * @param callable|null $reverseCallback
 * @param array         $context
 *
 * @return mixed
 */
function toggle(string $feature, $callback = null, $reverseCallback = null, array $context = [])
{
    static $toggle;

    if (!$toggle) {
        $toggle = new Toggle(toggleConfig());
    }

    if (empty($context) && is_array($callback) && !is_callable($callback)) {
        $context = $callback;
        $callback = null;
    }

    if (empty($context) && is_array($reverseCallback) && !is_callable($reverseCallback)) {
        $context = $reverseCallback;
        $reverseCallback = null;
    }

    $active = $toggle->isActive($feature, $context);

    if (null === $callback) {
        return $active;
    }

    if ($active) {
        return $toggle->execute($callback);
    } elseif (null !== $reverseCallback) {
        return $toggle->execute($reverseCallback);
    }
}