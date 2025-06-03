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
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Yaml\Yaml;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_readable;
use function sprintf;

class YamlFileStorage extends ArrayStorage implements PersistentStorageInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        if (! class_exists(Yaml::class)) {
            throw new Exception('The symfony/yaml component is needed in order to load config from yaml file');
        }

        if (! is_file($filePath) || ! is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('The file %s either does not exist, or is not readable', $filePath));
        }

        $this->filePath = $filePath;

        $content = file_get_contents($this->filePath);

        if ($content !== false) {
            /** @var array<string, bool|string|int|Expression|object|callable|null> $config */
            $config = Yaml::parse($content);
            parent::__construct($config);
        }
    }

    public function set(string $key, bool $value): bool
    {
        $this->config[$key] = $value;

        file_put_contents($this->filePath, Yaml::dump($this->config));

        return $value;
    }
}
