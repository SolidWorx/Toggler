<?php

declare(strict_types=1);

/*
 * This file is part of SolidWorx Toggler project.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidWorx\Toggler\Storage;

use Exception;
use InvalidArgumentException;
use function get_debug_type;
use function is_array;
use function is_file;
use function is_string;
use function pathinfo;
use function sprintf;
use function strtolower;

final class StorageFactory
{
    /**
     * @param mixed $config
     *
     * @throws InvalidArgumentException|Exception
     */
    public static function factory($config): StorageInterface
    {
        switch (true) {
            case $config instanceof StorageInterface:
                return $config;

            case is_array($config):
                return new ArrayStorage($config);

            case is_string($config) && is_file($config):
                $extension = strtolower(pathinfo($config, PATHINFO_EXTENSION));

                if ($extension === 'yml') {
                    return new YamlFileStorage($config);
                }

                if ($extension === 'php') {
                    return new ArrayStorage(require $config);
                }

                throw new InvalidArgumentException(sprintf('File with extension %s is not supported', $extension));
            default:
                throw new InvalidArgumentException(sprintf('The 1st argument for %s expects an array, string or instance of StorageInterface, %s given', __METHOD__, get_debug_type($config)));
        }
    }
}
