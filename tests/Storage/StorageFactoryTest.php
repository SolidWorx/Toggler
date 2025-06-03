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

namespace SolidWorx\Toggler\Tests\Storage;

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\{
    ArrayStorage,
    StorageFactory,
    YamlFileStorage
};
use Symfony\Component\Yaml\Yaml;

class StorageFactoryTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
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
            ->withContent('<?php return ' . var_export($features, true) . ';')
            ->at($this->root);

        $this->assertInstanceOf(ArrayStorage::class, StorageFactory::factory(new ArrayStorage($features)));
        $this->assertInstanceOf(ArrayStorage::class, StorageFactory::factory($features));
        $this->assertInstanceOf(YamlFileStorage::class, StorageFactory::factory($yamlFile->url()));
        $this->assertInstanceOf(ArrayStorage::class, StorageFactory::factory($phpFile->url()));
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

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File with extension txt is not supported');

        StorageFactory::factory($file->url());
    }

    public function testInvalidConfigType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The 1st argument for ' . StorageFactory::class . '::factory expects an array, string or instance of StorageInterface, bool given');

        StorageFactory::factory(true);
    }
}
