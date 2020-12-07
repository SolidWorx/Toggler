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

namespace SolidWorx\Toggler\Tests\Storage;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\{
    ArrayStorage,
    StorageFactory,
    StorageInterface,
    YamlFileStorage
};
use Symfony\Component\Yaml\Yaml;

class StorageFactoryTest extends TestCase
{
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testFactory(): void
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $yamlFile = vfsStream::newFile('file.yml')
            ->withContent(Yaml::dump($features))
            ->at($this->root);

        $phpFile = vfsStream::newFile('file.php')
            ->withContent('<?php return '.var_export($features, true).';')
            ->at($this->root);

        self::assertInstanceOf(ArrayStorage::class, StorageFactory::factory($features));
        self::assertInstanceOf(YamlFileStorage::class, StorageFactory::factory($yamlFile->url()));
        self::assertInstanceOf(ArrayStorage::class, StorageFactory::factory($phpFile->url()));
    }

    public function testInvalidConfigFile(): void
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $file = vfsStream::newFile('file.txt')
            ->withContent(Yaml::dump($features))
            ->at($this->root);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File with extension txt is not supported');

        StorageFactory::factory($file->url());
    }

    public function testInvalidConfigType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The 1st argument for '.StorageFactory::class.'::factory expects an array, string or instance of StorageInterface, boolean given');

        StorageFactory::factory(true);
    }
}
