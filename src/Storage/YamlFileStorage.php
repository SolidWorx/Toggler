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

use Exception;
use function file_get_contents;
use function file_put_contents;
use InvalidArgumentException;
use function is_file;
use function is_readable;
use function sprintf;
use Symfony\Component\Yaml\Yaml;

class YamlFileStorage extends ArrayStorage implements PersistenStorageInterface
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $filePath)
    {
        if (!class_exists(Yaml::class)) {
            throw new Exception('The symfony/yaml component is needed in order to load config from yaml file');
        }

        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('The file %s either does not exist, or is not readable', $filePath));
        }

        $this->filePath = $filePath;

        $content = file_get_contents($this->filePath);

        if (false !== $content) {
            parent::__construct(Yaml::parse($content));
        }
    }

    public function set(string $key, bool $value): bool
    {
        $this->config[$key] = $value;

        file_put_contents($this->filePath, Yaml::dump($this->config));

        return $value;
    }
}
