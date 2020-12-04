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

namespace SolidWorx\Toggler\Storage;

final class StorageFactory
{
    /**
     * @param $config
     *
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
            case is_string($config) && is_file($config):
                $extension = strtolower(pathinfo($config, PATHINFO_EXTENSION));

                if ('yml' === $extension) {
                    return new YamlFileStorage($config);
                }

                if ('php' === $extension) {
                    return new ArrayStorage(require_once $config);
                }

                throw new \InvalidArgumentException(sprintf('File with extension %s is not supported', $extension));
            default:
                throw new \InvalidArgumentException(sprintf('The 1st argument for %s expects an array, string or instance of StorageInterface, %s given', __METHOD__, is_object($config) ? get_class($config) : gettype($config)));
        }
    }
}
